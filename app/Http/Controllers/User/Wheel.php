<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use \Exception;
use Cache;
use Funcs;
use DB;

class Wheel extends Controller
{
    public function get(Request $req)
    {
        $user = $req['user'];
        $data = DB::table('wheel')->orderBy('id', 'asc')->get(['text','bg']);
        $check = DB::table('wheel_player')->where('userid', $user->userid)->first();
        if ($check) {
            $date = Carbon::now()->format('d-m-Y');
            $free = $check->free;
            $played = $check->date == $date ? $check->played : 0;
        } else {
            $free = 0;
            $played = 0;
        }
        return [
            'status' => 1,
            'balance' => $user->balance,
            'cost' => env('WHEEL_ROUND_COST'),
            'free' => $free,
            'played' => env('WHEEL_DAILY_LIMIT') - $played,
            'data' => $data
        ];
    }

    public function post(Request $req)
    {
        $user = $req['user'];
        $x = (int) $req->get('x');
        if ($x == 0) {
            return ['status' => 0, 'message' => 'Enter amount to draw.'];
        }
        $cost =  (int) env("WHEEL_ROUND_COST");
        $deduct = $x * $cost;
        if ($user->balance < $deduct) {
            return ['status' => 10, 'message' => 'Insufficiend balance!'];
        }
        $date = Carbon::now()->format('d-m-Y');
        $wU = DB::table('wheel_player')->where('userid', $user->userid);
        $wCheck = $wU->first();
        $played = 1;
        if ($wCheck) {
            if ($wCheck->date == $date && $wCheck->played >= env('WHEEL_DAILY_LIMIT')) {
                return ['status' => 0, 'message' => 'Daily playing limit exceeded'];
            }
            if ($wCheck->date == $date) {
                $played = $wCheck->played + 1;
                $wU->increment('played', 1);
            } else {
                $wU->update(['played' => 1, 'date' => $date]);
            }
        } else {
            DB::table('wheel_player')->insert([
                'userid' => $user->userid,
                'played' => 1,
                'date' => $date
            ]);
        }
        $arr = DB::table('wheel')->orderBy('id', 'asc')->get();
        $keys = array();
        for ($i = 0; $i < count($arr); $i++) {
            for ($u = 0; $u < (5 - $arr[$i]->difficulty); $u++) {
                $keys[] = $i;
            };
        }
        $elected = $keys[rand(0, count($keys) - 1)];
        $data = $arr[$elected];
        $free = 0;
        $card = 0;
        if ($wCheck && $wCheck->free > 0) {
            if ($wCheck->free >= $x) {
                $free = $wCheck->free - $x;
                $wU->decrement('free', $x);
                $deduct = 0;
            } else {
                $deduct = ($x - $wCheck->free) * $cost;
                $wU->update(['free' => 0]);
            }
        }
        $balance = $user->balance - $deduct;
        Funcs::deductGamePoints($user, 'Wheel', $deduct);
        if ($data->id == 1 && env('WHEEL_FREE_CHANCE') == 1) {
            $free += $x;
            DB::table('wheel_player')->where('userid', $user->userid)->increment('free', $x);
            $card = 'f';
        } elseif ($data->card_id != 0) {
            if ($x == 1) {
                $cid = Funcs::addCard($user->userid, $data->card_id, 1);
                $cd = DB::table('scratcher_game')->where('id', $data->card_id)->first();
                $card = $cd->name.'@@'.$cd->image.'@@'.$cd->coord.'@@'.$cid;
            } else {
                Funcs::addCard($user->userid, $data->card_id, $x);
                $card = 'm-'.$data->card_id;
            }
        } else {
            $card = 'nf';
            $balance += $data->text * $x;
            $user->increment('balance', $data->text * $x);
            if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                Funcs::leaderboard($user->name, $user->userid, $data->text * $x);
            }
            DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'Wheel'], ['points' => DB::raw("points + '$data->text'")]);
        }
        $res = [
            'status' => 1,
            'balance' => $balance,
            'free' => $free,
            'card' => $card,
            'played' => env('WHEEL_DAILY_LIMIT') - $played,
            'mark' => DB::table('wheel')->where('id', '<=', $data->id)->count() - 1
        ];
        if ($data->id == 1 && $x != 1) {
            $res['message'] = str_ireplace($data->text, $x .'X '. $data->text, $data->message);
        } elseif ($data->card_id != 0) {
            $res['message'] = str_ireplace('1', $x, $data->message);
        } elseif (is_numeric($data->text)) {
            $txt = abs($data->text);
            $res['message'] = str_replace($txt, ($txt * $x), $data->message);
        } else {
            $res['message'] = $data->message;
        }
        return $res;
    }
}
