<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use Cache;
use Funcs;
use DB;

class Connect extends Controller
{
    public function geo()
    {
        return Cache::rememberForever('connect_geo', function () {
            return ['message' => DB::table('misc')->where('name', 'geo_api')->first()->data];
        });
    }

    public function check(Request $req)
    {
        $key = Funcs::encKey();
        try {
            $ip = \Request::ip();
            $cc = strtolower($req->json('cc'));
            if (!Funcs::countryExist($cc)) {
                return Cache::remember('connect_country_err', 3600, function () use ($key) {
                    return ['size' => $key, 'data' => Funcs::enc(json_encode(['status' => 0, 'message' => 'Unsupported country!']))];
                });
            }
            Funcs::addOnline($cc);
            Cache::forget('connect_offers');
            $offers = Cache::rememberForever('connect_offers', function () {
                return [
                    'offerwall_sdk' => DB::table('offerwalls')->where('type', 1)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id')
                        ->get(['data','name','title','description','network_image']),
                    'offerwall_cpa' => DB::table('offerwalls')->where('type', 2)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id')
                        ->get(['offerwalls.id','data','name','title','description','network_image']),
                    'offerwall_cpv' => DB::table('offerwalls')->where('type', 3)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id')
                        ->get(['data','name','title','description','network_image']),
                    'offerwall_web' => DB::table('offerwalls')->where('type', 4)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id')
                        ->get(['data','name','title','description','network_image'])
                ];
            });
            $currency = env('CURRENCY_NAME');
            $root = env('ROOT_BLOCK');
            $a_root = env('AUTO_BAN_ROOT');
            $vpn = env('VPN_BLOCK');
            $m_vpn = env('VPN_MONITOR');
            $interval = (int)env('BALANCE_INTERVAL');
            $conversion = env('CASHTOPTS') * env('PAY_PCT') / 100;
            $user = Funcs::userinfo($req);
            $status = -1;
            if ($req->has('mid')) {
                $mid = $req->get('mid');
                $gmid = Cache::get('gmid', '1234567');
                if ($gmid == $mid) {
                    $gMsg = ['title' => '', 'desc' => '', 'mid' => $gmid];
                } else {
                    $gMsg = Cache::rememberForever('global_msg', function () use ($gmid) {
                        $gmCheck = DB::table('misc')->where('name', 'global_msg')->first();
                        if ($gmCheck) {
                            $dta = unserialize($gmCheck->data);
                            Cache::put('gmid', $dta['mid']);
                        } else {
                            $dta = ['title' => '', 'desc' => '', 'mid' => $gmid];
                        }
                        return $dta;
                    });
                }
            } else {
                $gMsg = ['title' => '', 'desc' => '', 'mid' => ''];
            }
            if ($user) {
                Funcs::updateFCT($user->userid, $user->email, $req->json('fid'));
                $banned = Funcs::isBanned($user, $user->device_id);
                if ($banned) {
                    return ['size' => $key, 'data' => Funcs::enc(json_encode(['status' => -2, 'message' => $banned->reason]))];
                }
                if (strtolower($user->country) != $cc && env('BAN_CC_CHANGE') == 1) {
                    if ($user->device_id != 'none') {
                        DB::table('banned_users')->insert([
                            'userid' => $user->userid,
                            'reason' => "Auto ban CC",
                            'device_id' => $user->device_id
                        ]);
                    } else {
                        DB::table('banned_users')->insert([
                            'userid' => $user->userid,
                            'reason' => "Auto ban CC"
                        ]);
                    }
                    return Cache::remember('connect_ban_cc', 3600, function () use ($key) {
                        return ['size' => $key, 'data' => Funcs::enc(json_encode(['status' => -2, 'message' => "Auto ban CC"]))];
                    });
                }
                if (env('AUTO_BAN_MULTI') == 1) {
                    $toBan = DB::table('users')->where('device_id', $user->device_id)->get();
                    foreach ($toBan as $ban) {
                        DB::table('banned_users')->updateOrInsert(
                            ['userid' => $ban->userid],
                            ['reason' => 'Banned for creating multiple accounts.', 'device_id' => $ban->device_id]
                        );
                    }
                    return Cache::remember('connect_ban_multi', 3600, function () use ($key) {
                        return ['size' => $key, 'data' => Funcs::enc(json_encode(['status' => -2, 'message' => "Banned for creating multiple accounts."]))];
                    });
                }
                $status = 1;
            }
            $now = Carbon::now()->timestamp;
            $tour = Cache::remember('connect_tour', 300, function () {
                //$tour = Cache::rememberForever('connect_tour', function () {
                return [
                    'name' => str_replace('_', ' ', env('TOUR_NAME')),
                    'time' => (int) env('TOUR_BEGIN_TIME'),
                    'fee' => env('TOUR_ENTRY_FEE'),
                    'reward' => env('TOUR_REWARD'),
                    'pub' => env('TOUR_PUB_TIMESTAMP')
                ];
            });
            $res = [
                'status' => $status,
                'ip' => $ip,
                'currency' => $currency,
                'conversion' => $conversion,
                'offers' => $offers,
                'r' => $root,
                'a_r' => $a_root,
                'v' => $vpn,
                'v_m' => $m_vpn,
                'g_msg' => $gMsg,
                'interval' => $interval,
                'time' => $now,
                'tour' => $tour,
                'vc' => env('USER_VERSIONCODE'),
                'ut' => env('USER_FORCE_UPDATE'),
                'html_reload' => env('HTML_GAME_REFRESH')
            ];
            if ($status == 1) {
                $e = DB::table('tour_player')->where('userid', $user->userid)->first();
                $res['enroll'] = $e ? 1 : 0;
            }
            return ['size' => $key, 'data' => Funcs::enc(json_encode($res))];
        } catch (\Exception $e) {
            return ['size' => $key, 'data' => Funcs::enc(json_encode(['status' => 0, 'message' => $e->getMessage()]))];
            return Cache::remember('connect_general_err', 3600, function () use ($key) {
                return ['size' => $key, 'data' => Funcs::enc(json_encode(['status' => 0, 'message' => 'Unexpected error occurred!']))];
            });
        }
    }
}
