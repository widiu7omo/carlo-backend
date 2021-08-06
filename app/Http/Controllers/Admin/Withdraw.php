<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Funcs;
use DB;

class Withdraw extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $pending = DB::table('gate_request')->where('is_completed', 0)
                        ->orderBy('created_at', 'desc')
                        ->paginate(3, ['*'], 'p');
        $completed = DB::table('gate_request')->where('is_completed', 1)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10, ['*'], 'c');
        $wd = ['pending' => $pending, 'completed' => $completed];
        return view('admin.withdraw', compact('wd'));
    }

    public function proceed(Request $req)
    {
        $db = DB::table('gate_request')->where('is_completed', 0)->where('id', $req->get('id'));
        $check = $db->first();
        if ($check) {
            Funcs::deductGamePoints($check->userid, 'Redeemption', $check->points, false);
        }
        $db->update(['is_completed' => 1]);
        return back()->with('success', "Withdrawal marked as processed!");
    }
    public function info(Request $req)
    {
        $stats = [];
        $wd = DB::table('gate_request')->where('id', $req->get('wid'))->first();
        if ($wd) {
            $stats['wd'] = 1;
            $user = DB::table('users')->where('userid', $wd->userid)->first();
            if ($user) {
                $stats['user'] = 1;
                $ban_check = DB::table('banned_users')->where('userid', $user->userid)->orWhere('device_id', $user->device_id)->exists();
                if ($ban_check) {
                    $stats['banned'] = 1;
                }
                if ($user->balance > -1 && $user->pending >= $wd->points) {
                    $stats['balance'] = 1;
                }
                $h_bal = DB::table('hist_activities')->where('userid', $user->userid)->sum('points');
                $g_db = DB::table('hist_game')->where('userid', $user->userid);
                $g_bal = $g_db->sum('points') - $g_db->sum('deducted');
                if ($h_bal + $g_bal == $user->balance + $user->pending) {
                    $stats['history'] = 1;
                } else {
                    $missed = $user->balance + $user->pending - $h_bal - $g_bal;
                    if ($missed > 0) {
                        $stats['history'] = $missed . ' '  . strtolower(env('CURRENCY_NAME')) . 's in balance without any trace.';
                    } else {
                        $stats['history'] = (- $missed) . ' '  . strtolower(env('CURRENCY_NAME')) . 's found in history but missing in balance.';
                    }
                }
                if (strtolower($wd->country) == strtolower($user->country)) {
                    $stats['country'] = 1;
                } else {
                    $stats['country'] = 'user registered from "' . \Funcs::getCountry($user->country) . '" but withdrawal request initiated from "' . \Funcs::getCountry($wd->country) . '"';
                }
            }
        }
        return $stats;
    }

    public function discard(Request $req)
    {
        $reason = $req->get('reason');
        if (strpos($reason, ';@') !== false) {
            return back()->with('error', "Invalid characters exist in your refusing reason.");
        }
        $d = DB::table('gate_request')->where('id', $req->post('id'));
        $wd = $d->first();
        if ($wd->is_completed == 0) {
            $u = DB::table('users')->where('userid', $wd->userid);
            $u->increment('balance', $wd->points);
            $u->decrement('pending', $wd->points);
            if ($reason == null || $reason == '') {
                $d->update(['is_completed' => 2,'message' => 'No reason provided.']);
            } else {
                $d->update(['is_completed' => 2,'message' => $reason]);
            }
            /*
            if ($req->has('reason') && $req->get('reason') != '') {
                DB::table('message')->insert(['userid' => $wd->userid,'title' => 'Withdrawal rejected!', 'msg' => $req->get('reason')]);
                $u->increment('has_notification', 1);
            }
            $d->delete();
            */
            return back()->with('success', "Withdrawal request rejected and ".strtolower(env('CURRENCY_NAME'))."s returned to the user balance!");
        } else {
            return back()->with('error', "This withdrawal already processed by the system.");
        }
    }
}
