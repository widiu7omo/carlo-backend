<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Funcs;
use DB;
use App\User;

class Userinfo extends Controller
{
    public function info(Request $req)
    {
        $user = $req['user'];
        return ['status' => 1, 'name' => $user->name,'avatar' => $user->avatar];
    }

    public function fid(Request $req)
    {
        $user = $req['user'];
        Funcs::updateFCT($user->userid, $user->email, $req->json('fid'));
        return ['status' => 1, 'message' => 'updated'];
    }

    public function balance(Request $req)
    {
        $user = $req['user'];
        $res = array('status' => 1, 'b' => $user->balance,'u' => $user->name);
        if ($req->get('type') == "1" && $user->has_notification > 0) {
            $user->decrement('has_notification', $user->has_notification);
            $msg = DB::table('message')->where('userid', $user->userid);
            $res['n'] = $msg->get(['title','msg','date']);
            $msg->delete();
        }
        return $res;
    }

    public function profile(Request $req)
    {
        $user = $req['user'];
        if ($user->refby == null || $user->refby == 'none') {
            $inv = '-none-';
        } else {
            $chk = DB::table('users')->where('userid', $user->refby)->first();
            $inv = $chk ? $chk->name : '-none-';
        }
        return [
            'status' => 1,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'code' => $user->userid,
            'inv' => $inv,
            'cc' => $user->country
        ];
    }

    public function profileChange(Request $req)
    {
        $user = $req['user'];
        $type = $req->json('type');
        $data = $req->json('data');
        if ($type == '1') {
            if (strlen($data) < 300) {
                DB::table('users')->where('userid', $user->userid)->update(['avatar' => $data]);
                return ['status' => 1, 'message' => 'update successfull.'];
            } else {
                return ['status' => 0, 'message' => 'URL length is too long!'];
            }
        } elseif ($type == '2') {
            if (strlen($data) < 50) {
                DB::table('users')->where('userid', $user->userid)->update(['name' => $data]);
                return ['status' => 1, 'message' => 'update successfull.'];
            } else {
                return ['status' => 0, 'message' => 'Text length is too long!'];
            }
        } elseif ($type == '3') {
            if (strlen($data) != 13) {
                return ['status' => 0, 'message' => 'Invalid referral code!'];
            }
            return Funcs::addref($user, $data);
        }
    }

    public function arGet(Request $req)
    {
        $user = $req['user'];
        $reward = DB::table('activity_reward')->where('active', 1)->orderBy('id', "asc")->get(['id','name','max']);
        $done = 0;
        $isDone = 0;
        if ($user->done_ar != null) {
            $ar = explode('||', $user->done_ar);
            if (Carbon::parse($ar[1])->isToday()) {
                $done = $ar[0]-1;
                $isDone = 1;
            } else {
                $done = $ar[0];
            }
        }
        return ['status' => 1, 'rewards' => $reward, 'done' => $done, 'is_done' => $isDone];
    }

    public function arReward(Request $req)
    {
        $user = $req['user'];
        $id = (int) $req->get('id');
        $done_ar = 0;
        $date = null;
        if ($user->done_ar != null) {
            $aa = explode('||', $user->done_ar);
            $done_ar = (int) $aa[0];
            $date = $aa[1];
        }
        if ($done_ar + 1 != $id) {
            return ['status' => 0, 'message' => 'You cannot open this reward vault!'];
        }
        $reward = DB::table('activity_reward')->where('active', 1)->where('id', $id+1)->first();
        if ($reward) {
            if ($date != null && Carbon::parse($date)->isToday()) {
                return ['status' => 0, 'message' => 'You already opened a vault. Come back tomorrow.'];
            }
            $amt = rand($reward->min, $reward->max);
            DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'ActivityReward'], ['points' => DB::raw("points + '$amt'")]);
            DB::table('users')->where('id', $user->id)->update([
                'balance' => $user->balance + $amt,
                'done_ar' => $id . '||' . Carbon::now()
            ]);
            return ['status' => 1, 'message' => 'You got rewarded', 'amount' => $amt];
        } else {
            return ['status' => 0, 'message' => 'Vault not found!'];
        }
    }

    public function autobanRoot(Request $req)
    {
        if (env('AUTO_BAN_ROOT') == 1) {
            $user = $req['user'];
            DB::table('banned_users')->updateOrInsert(
                ['userid' => $user->userid],
                ['reason' => 'Banned for using rooted device.', 'device_id' => $user->device_id]
            );
        }
    }

    public function vpnMonitor(Request $req)
    {
        if (env('AUTO_BAN_VPN') == 1) {
            $user = $req['user'];
            DB::table('banned_users')->updateOrInsert(
                ['userid' => $user->userid],
                ['reason' => 'Banned for using VPN.', 'device_id' => $user->device_id]
            );
        } elseif (env('VPN_MONITOR') == 1) {
            $user = $req['user'];
            $check = DB::table('vpn_monitor')->where('userid', $user->userid);
            if ($check->first()) {
                $check->increment('attempted', 1);
            } else {
                DB::table('vpn_monitor')->insert([
                    'userid' => $user->userid,
                    'name' => $user->name,
                    'avatar' => $user->avatar == null ? 'none' : $user->avatar,
                    'attempted' => 1
                ]);
            }
        }
    }

    public function refView()
    {
        return ['status' => 1, 'ref' => env('REF_LINK_REWARD'), 'user' => env('REF_USER_REWARD')];
    }

    public function refHistory(Request $req)
    {
        $user = $req['user'];
        $db = DB::table('hist_activities')
                    ->where('userid', $user->userid)
                    ->where('network', 'referral')
                    ->orderBy('id', 'desc')
                    ->get();
        $data = [];
        foreach ($db as $d) {
            $uid = str_replace('For referring: ', '', $d->note);
            $check = DB::table('users')->where('userid', $uid)->first();
            if ($check) {
                array_push($data, [
                    'image' => $check->avatar,
                    'name' => $check->name,
                    'date' => Carbon::parse($d->created_at)->timestamp
                ]);
            }
        }
        return ['status' => 1, 'message' => $data];
    }
}
