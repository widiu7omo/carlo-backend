<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
use DB;

class Misc extends Controller
{
    public function ranking(Request $req)
    {
        //Cache::forget('ranking');
        if (Cache::has('ranking')) {
            $res = Cache::get('ranking');
        } else {
            $userid = $req['user']->userid;
            $today = Carbon::now()->format('d-m');
            $yesterday = Carbon::yesterday()->format('d-m');
            DB::table('leaderboard')
                ->whereNotIn('date_cur', [$today, $yesterday])
                ->orWhereNotIn('date_prv', [$today, $yesterday])
                ->delete();
            $check = DB::table('misc')->where('name', 'leaderboard')->first();
            $db = DB::table('leaderboard')
                    ->where('date_prv', $yesterday)
                    ->orderby('score_prv', 'DESC')
                    ->limit(env('LEADERBOARD_LIMIT'))
                    ->get();
            $res = array();
            if ($check && $yesterday == $check->data) {
                foreach ($db as $d) {
                    $y = $d->userid == $userid ? "y" : "n";
                    $avatar = DB::table('users')->where('userid', $d->userid)->first()->avatar;
					if ($avatar == null || $avatar == '') {
                        $avatar = 'none';
                    }
                    array_push($res, ['y' => $y, 'a' => $avatar, 'n' => $d->name, 's' => $d->score_prv, 'r' => $d->reward]);
                }
            } else {
                if ($check) {
                    DB::table('misc')->where('name', 'leaderboard')->update(['data' => $yesterday]);
                } else {
                    DB::table('misc')->insert(['name' => 'leaderboard','data' => $yesterday]);
                }
                $pct = explode(',', env('LEADERBOARD_PCT'));
                $totalAmt = (int) env('LEADERBOARD_REWARD');
                for ($i = 0; $i < min(count($pct), count($db)); $i++) {
                    $uid = $db[$i]->userid;
                    $amt = round($totalAmt * (int) $pct[$i] / 100);
                    DB::table('leaderboard')->where('userid', $uid)->update(['reward' => $amt]);
                    $u = DB::table('users')->where('userid', $uid);
                    $user = $u->first();
                    $u->update(['balance' => $user->balance + $amt]);
                    DB::table('hist_activities')->insert([
                        'userid' => $user->userid,
                        'network' => 'ranking',
                        'note' => 'Ranked '.($i + 1).' on '.$yesterday,
                        'points' => $amt
                    ]);
                    $y = $user->userid == $userid ? "y" : "n";
                    $avatar = DB::table('users')->where('userid', $d->userid)->first()->avatar;
					if ($avatar == null || $avatar == '') {
                        $avatar = 'none';
                    }
                    array_push($res, ['y' => $y, 'a' => $avatar, 'n' => $user->name, 's' => $db[$i]->score_prv, 'r' => $amt]);
                }
            }
            Cache::put('ranking', $res, 3600);
        }
        return['status' => 1, 'rank' => $res];
    }

    public function faq()
    {
        return ['status' => 1, 'faq' => DB::table('support_faq')->get(['question','answer'])];
    }

    public function tos()
    {
        try {
            $p = file_get_contents(resource_path('views')."/privacy.blade.php");
            $privacy = str_replace(["@extends('privacy_inc') @section('privacy')\r\n","\r\n@endsection"], '', $p);
            $t = file_get_contents(resource_path('views')."/terms.blade.php");
            $terms = str_replace(["@extends('terms_inc') @section('terms')\r\n","\r\n@endsection"], '', $t);
        } catch (\Exception $e) {
            $privacy = 'Could not load the file. Make sure "resources\views\privacy.blade.php" got full read-write permission (0777)';
            $terms = 'Could not load the file. Make sure "resources\views\terms.blade.php" got full read-write permission (0777)';
        }
        return ['status' => 1, 't' => $terms, 'p' => $privacy];
    }
}
