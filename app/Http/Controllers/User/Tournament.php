<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Funcs;
use Cache;
use DB;

class Tournament extends Controller
{
    public function enroll(Request $req)
    {
        try {
            $user = $req['user'];
            $fee = (int) env('TOUR_ENTRY_FEE');
            if ($user->balance < $fee) {
                return ['status' => 10, 'message' => 'Insufficiend balance!'];
            }
            $chk = DB::table('tour_player')->where('userid', $user->userid)->first();
            if ($chk) {
                return ['status' => 2, 'message' => 'Already enrolled!'];
            } else {
                Funcs::deductGamePoints($user, 'tournament', $fee);
                DB::table('tour_player')->insert([
                    'userid' => $user->userid, 
                    'name' => $user->name, 
					'qa' => '',
                    'avatar' => $user->avatar == null ? 'none' : $user->avatar
                ]);
                return ['status' => 1, 'message' => 'Request accepted.'];
            }
        } catch (\Exception $e) {
            return ['status' => 0, 'message' => $e->getMessage()];
        }
    }

    public function get(Request $req)
    {
        if (Cache::has('tour_q')) {
            return Cache::get('tour_q');
        }
        $beginTime = (int) env('TOUR_BEGIN_TIME');
        if ($beginTime == 0) {
            return ['status' => 0, 'message' => 'Tournament ended!'];
        }
        $db = DB::table('tour_question')->orderBy('id', "ASC")->get();
        $time_counter = 0;
        $now = Carbon::now()->timestamp;
        if ($beginTime > $now) {
            $q = ['status' => 0, 'message' => 'Tournament yet to start!'];
            Cache::put('tour_q', $q, $beginTime - $now);
            return $q;
        }
        $count = 0;
        foreach ($db as $d) {
            $time_counter += $d->time;
            $count ++;
            if ($now <= $beginTime + $time_counter) {
                $t =  $beginTime + $time_counter - $now;
                $q = ['status' => 1, 'id' => $d->id, 'q' => $d->question, 'o' => $d->options, 't' => $beginTime + $time_counter, 's' => $d->score, 'c' => $db->count(), 'l' => $count];
                Cache::put('tour_q', $q, $t);
                return $q;
            }
        }
        Funcs::setEnv('TOUR_BEGIN_TIME', 0, false);
        Funcs::setEnv('TOUR_PUB_TIMESTAMP', $now + env('TOUR_PUB_TIME') * 60);
        $q = ['status' => 2, 'message' => 'That is all for now. We will publish the result soon.'];
        Cache::put('tour_q', $q, 60);
        Cache::forget('connect_tour');
        return $q;
    }

    public function ans(Request $req)
    {
        $now = Carbon::now()->timestamp;
        if (env('TOUR_BEGIN_TIME') == 0) {
            Funcs::setEnv('TOUR_PUB_TIMESTAMP', $now + env('TOUR_PUB_TIME') * 60);
            return ['status' => 2, 'message' => 'Invalid quiz or time is over!'];
        }
        if (!Cache::has('tour_l')) {
            $totalTime = DB::table('tour_question')->get()->sum('time');
            $t = (int) env('TOUR_BEGIN_TIME') + $totalTime;
            if ($t > $now) {
                Cache::put('tour_l', 'y', $t - $now + 5);
                Funcs::setEnv('TOUR_PUB_TIMESTAMP', 0);
            } else {
                Funcs::setEnv('TOUR_BEGIN_TIME', 0, false);
                Funcs::setEnv('TOUR_PUB_TIMESTAMP', $now + env('TOUR_PUB_TIME') * 60);
                Cache::forget('connect_tour');
                return ['status' => 2, 'message' => 'Invalid quiz or time is over!'];
            }
        }
        $q = $req->get('q');
        $a = $req->get('a');
        if (strlen($q) > 4 || strlen($a) > 4) {
            return ['status' => 0, 'message' => 'Wrong answer type!'];
        }
        if (!is_numeric($q) || !is_numeric($a)) {
            return ['status' => 0, 'message' => 'Invalid data!'];
        }
        $user = $req['user'];
        $db = DB::table('tour_player')->where('userid', $user->userid);
        $chk = $db->first();
        if ($chk) {
            $qInfo = DB::table('tour_question')->where('id', $q)->first();
            if (strpos($chk->qa, ',' . $q . '_') !== false) {
                if ($qInfo && $a == $qInfo->answer) {
                    $db->update([
                        'qa' => ',' . $q . '_' . $a,
                        'correct' => 1,
                        'marks' => $qInfo->score
                    ]);
                } else {
                    $db->update([
                        'qa' => ',' . $q . '_' . $a,
                        'correct' => 0,
                        'marks' => 0
                    ]);
                }
            } else {
                if ($qInfo && $a == $qInfo->answer) {
                    $db->update([
                        'qa' => $chk->qa . ',' . $q . '_' . $a,
                        'correct' => $chk->correct + 1,
                        'marks' => $qInfo->score + $chk->marks
                    ]);
                } else {
                    $db->update(['qa' => $chk->qa . ',' . $q . '_' . $a]);
                }
            }
            return ['status' => 1, 'message' => 'Try next question'];
        } else {
            return ['status' => 0, 'message' => 'You did not register for this tournament'];
        }
    }

    public function rank(Request $req)
    {
        //Cache::forget('tour_rank');
        if (Cache::has('tour_rank')) {
            $rank = Cache::get('tour_rank');
            $wons = Cache::get('tour_rwd');
        } else {
            $now = Carbon::now()->timestamp;
            if ($now < env('TOUR_PUB_TIMESTAMP')) {
                return ['status' => 2, 'message' => 'Result will be published soon!'];
            }
            $rank = DB::table('tour_player')->orderBy('marks', 'DESC')->limit(env('TOUR_WINNER_COUNT'))->get(['userid','name','avatar','correct','marks']);
            Cache::forever('tour_rank', $rank);
            $totalAmt = (int) env('TOUR_REWARD');
            $pct = explode(',', env('TOUR_WINNER_PCT'));
            $wons = '';
            if (env('TOUR_RES_PUBLISHED') == 0) {
                Funcs::setEnv('TOUR_RES_PUBLISHED', 1);
                $tName = env('TOUR_NAME');
                $sze = min(count($rank), count($pct), (int) env('TOUR_WINNER_COUNT'));
                for ($i = 0; $i < $sze; $i++) {
                    //$amt = round($totalAmt * (int) $pct[$i] / 100);
                    $amt = $rank[$i]->marks < 1 ? 0 : round($totalAmt * (int) $pct[$i] / 100);      //if mark 0 then no reward
                    $user = DB::table('users')->where('userid', $rank[$i]->userid);
                    $user->increment('balance', $amt);
                    DB::table('hist_activities')->insert([
                        'userid' => $rank[$i]->userid,
                        'network' => 'tournament',
                        'note' => $tName,
                        'points' => $amt
                    ]);
                    $wons .= round($totalAmt * (int) $pct[$i] / 100) . ',';
                }
            } else {
                for ($i = 0; $i < count($pct); $i++) {
                    $wons .= round($totalAmt * (int) $pct[$i] / 100) . ',';
                }
            }
            Cache::forever('tour_rwd', $wons);
        }
        $user = $req['user'];
        $y = DB::table('tour_player')->where('userid', $user->userid)->first();
        if ($y) {
            $yRank = DB::table('tour_player')->where('marks', '>=', $y->marks)->count();
            $res = ['status' => 1, 'rwd' => $wons, 'y' => $yRank.';'.$y->correct.';'.$y->marks, 'r' => $rank];
        } else {
            $res = ['status' => 1, 'rwd' => $wons, 'y' => '0;0;0', 'r' => $rank];
        }
        return $res;
    }
}
