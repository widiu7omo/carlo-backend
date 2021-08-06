<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use \App\User;
use Funcs;
use DB;

class Postback extends Controller
{
    protected function Callback($request)
    {
        if (!is_numeric($request->payout)) {
            return 'invalid payout';
        }
        if ($request->tok != $request->savedtok) {
            return response('0');
        }
        if ($request->userid == 'visitor' || $request->userid == 'guest') {
            return response('1');
        }
        if (!$request->userid || $request->userid == '' || strlen($request->userid) != 13) {
            return response('0');
        }
        $payout = $request->is_video ? $request->payout : round($request->payout * env('CASHTOPTS') * env('PAY_PCT') / 100);
        $user = User::where('userid', $request->userid)->first();
        if ($user) {
            $user->increment('balance', $payout);
            if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                Funcs::leaderboard($user->name, $user->userid, $payout);
            }
            if ($request->network == 'Ironsrc') {
                $oid = '-';
                $ret = '<status> '.$request->offer_id.':OK</status>';
            } elseif ($request->network == 'Adcolony') {
                $oid = $request->offer_id;
                $ret = 'vc_success';
            } else {
                $oid = $request->offer_id;
                $ret = '1';
            }
            if ($request->is_video) {
                $chk_db = DB::table('hist_activities')
                            ->where('userid', $user->userid)
                            ->where('network', 'video')
                            ->where('is_custom', 0);
                if ($chk_db->first()) {
                    $chk_db->increment('points', $payout);
                } else {
                    DB::table('hist_activities')->insert([
                        'userid' => $user->userid,
                        'network' => 'video',
                        'is_lead' => 1,
                        'is_custom' => 0,
                        'offerid' => 'vid',
                        'ip' => '-none-',
                        'points' => $payout
                    ]);
                }
            } else {
                DB::table('hist_activities')->insert([
                    'userid' => $user->userid,
                    'network' => $request->network,
                    'is_lead' => 1,
                    'is_custom' => $request->is_cpa ? 4 : 0,
                    'offerid' => $oid,
                    'ip' => $request->ip,
                    'points' => $payout
                ]);
            }
            if (env('EARNING_NOTIFICATION') == 1) {
                $cn = strtolower(env('CURRENCY_NAME'))."s";
                DB::table('message')->insert([
                    'userid' => $user->userid,
                    'title' => 'You got '.$cn,
                    'msg' => 'You just received +'.$payout.' '.$cn.' for completing "'.$reward->title.'"',
                ]);
                if ($user->has_notification == 0) {
                    $user->increment('has_notification', 1);
                }
            };
        }
        return response($ret, 200);
    }
    
    public function cpa(Request $req, $net)
    {
        try {
            $pb = DB::table('postbacks')->where('network_slug', $net)->first();
            if ($pb) {
                $ipKey = explode("=", $pb->param_ip);
                $offerId = explode("=", $pb->param_offerid);
                $send =  new \stdClass();
                $send->tok = $req->query('tok');
                $send->savedtok = $pb->param_tok;
                $send->payout = $req->query(strtok($pb->param_amount, '='));
                $send->userid = $req->query(strtok($pb->param_userid, '='));
                $send->network = $pb->network_name;
                $send->is_cpa = substr($pb->network_slug, -2) == '_a';
                $send->is_video = substr($pb->network_slug, -2) == '_v';
                if ($send->network == 'Ironsrc') {
                    $send->offer_id = $req->query($offerId[0]);
                } elseif ($offerId[1] == '' || $offerId[1] == 'blank') {
                    $send->offer_id = '-';
                } else {
                    $send->offer_id = $req->query($offerId[0]);
                }
                if ($ipKey[1] == '' || $ipKey[1] == 'blank') {
                    $send->ip = '-none-';
                } else {
                    $send->ip = $req->query($ipKey[0]);
                }
                return $this->Callback($send);
            } else {
                return 'Invalid url';
            }
        } catch (\Exception $e) {
            return 'Invalid request';
        }
    }

    public function premiumUrlOffers(Request $req)
    {
        try {
            $userid = $req->query('uid');
            $user = User::where('userid', $userid)->first();
            if ($user) {
                $db = DB::table('misc')->where('name', 'cpb')->first();
                $savedtok = $db ? $db->data : '';
                if ($req->query('tok') == $savedtok) {
                    $offerid = $req->query('offerid');
                    $ip = $req->query('ip') == null ? '-none-' : $req->query('ip');
                    $oDB = DB::table('offerwall_c')->where('offer_id', $offerid);
                    $offer = $oDB->first();
                    if ($offer && $offer->max > $offer->completed) {
                        $user->increment('balance', $offer->points);
                        if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                            Funcs::leaderboard($user->name, $user->userid, $offer->points);
                        }
                        $oDB->increment('completed', 1);
                        DB::table('hist_activities')->insert([
                            'userid' => $user->userid,
                            'network' => 'direct',
                            'is_lead' => 1,
                            'is_custom' => 1,
                            'offerid' => $offerid,
                            'ip' => $ip,
                            'points' => $offer->points
                        ]);
                        if (env('EARNING_NOTIFICATION') == 1) {
                            $cn = strtolower(env('CURRENCY_NAME'))."s";
                            DB::table('message')->insert([
                                'userid' => $user->userid,
                                'title' => 'Reward from premium offer',
                                'msg' => 'You just received +'.$offer->points.' '.$cn.' for completing "'.$offer->title.'"',
                            ]);
                            if ($user->has_notification == 0) {
                                $user->increment('has_notification', 1);
                            }
                        };
                        return "1";
                    } else {
                        return "0";
                    }
                } else {
                    return "0";
                }
            } else {
                return "0";
            }
        } catch (\Exception $e) {
            return 'Invalid request';
        }
    }
}