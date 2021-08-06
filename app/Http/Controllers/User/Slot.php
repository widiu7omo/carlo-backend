<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Funcs;

class Slot extends Controller
{
    public function get(Request $req)
    {
        $user = $req['user'];
        $data = \Cache::remember('slot_info', 86400, function () {
            return [
                'status' => 1,
                'c' => env('SLOT_COST'),
                'rw' => env('SLOT_ROWS'),
                'co' => env('SLOT_COLS'),
                'sp' => env('SLOT_SPEED'),
                'ic' => env('SLOT_ICONS'),
                'mx' => max(explode(',', env('SLOT_ICON_VAL'))) * env('SLOT_ROWS') * env('SLOT_COLS')
            ];
        });
        $data['b'] = $user->balance;
        $check = DB::table('slot_player')->where('userid', $user->userid)->first();
        $r = env('SLOT_DAILY_ROUND');
        if ($check) {
            if (Carbon::parse($check->updated_at)->isToday()) {
                $data['rm'] = $r - $check->round;
            } else {
                DB::table('slot_player')
                    ->where('userid', $user->userid)
                    ->update(['round' => 0, 'updated_at' => Carbon::now()]);
                $data['rm'] = $r;
            }
            $data['f'] = $check->free;
        } else {
            DB::table('slot_player')->insert([
                'userid' => $user->userid,
                'round' => 0,
                'updated_at' => Carbon::now()
            ]);
            $data['rm'] = $r;
            $data['f'] = 0;
        }
        return $data;
    }

    public function post(Request $req)
    {
        $user = $req['user'];
        $multiply = $req->get('m');
        if ($multiply == 0) {
            return ['status' => 0, 'message' => 'Check your multiplication.'];
        }
        $cost = env('SLOT_COST') * $multiply;
        if ($cost > $user->balance) {
            return ['status' => 0, 'message' => 'Insufficient balance!'];
        }
        $rws = env('SLOT_ROWS');
        $cls = env('SLOT_COLS');
        if ($rws != DB::table('misc')->where('name', 'slot_rows')->first()->data) {
            return ['status' => 0, 'message' => 'Yet to configure winning lines.'];
        }
        if ($cls != DB::table('misc')->where('name', 'slot_cols')->first()->data) {
            return ['status' => 0, 'message' => 'Yet to configure winning lines.'];
        }
        $userDb = DB::table('slot_player')->where('userid', $user->userid);
        $userData = $userDb->first();
        if ($userData->round >= env('SLOT_DAILY_ROUND')) {
            return ['status' => 0, 'message' => 'No more chance left for today. Try again tomorrow.'];
        }
        $max = env('SLOT_ICONS');
        $ival = env('SLOT_ICON_VAL');
        $nums = array();
        for ($i = 0; $i < $cls * $rws; $i++) {
            array_push($nums, rand(0, $max - 1));
        }
        //$nums = [0,2,2,1,5,1,3,1,5,3,0,3,0,1,0,0,3,3,3,7];
        $min_match = env('SLOT_MIN_MATCH');
        $db = DB::table('slot_game')->where('active', 1)->get();
        $winLPos = array();
        $lines = array();
        $won = 0;
        $lp = 0;
        $diff = 10 - env('SLOT_DIFFICULTY');       //max difficulty level will be 10
        for ($df = 0; $df < $diff; $df++) {
            for ($i = 0; $i < count($db); $i++) {
                $arr = explode(',', $db[$i]->line_array);
                $c = ['icon' => 0, 'pos' => array()];
                $tempCount = 1;
                for ($j = 0; $j < count($arr) - 1; $j++) {
                    $num = $nums[$arr[$j]];
                    if ($num == $nums[$arr[$j+1]]) {
                        $c['icon'] = $num;
                        array_push($c['pos'], $arr[$j]);
                        $tempCount += 1;
                        $lp = $j + 1;
                    //} elseif ($tempCount < $min_match) {
                        //$tempCount = 1;
                        //$c['pos'] = array();
                    } else {
                        break;
                    }
                }
                if ($tempCount >= $min_match) {
                    foreach ($c['pos'] as $p) {
                        array_push($winLPos, (int) $p);
                    }
                    array_push($winLPos, (int) $arr[$lp]);
                    if (count($c['pos']) == count(array_unique($c['pos']))) {
                        $won += ($tempCount * (int) explode(',', $ival)[$c['icon']]);
                        array_push($lines, (int) $i);
                    }
                }
                $lp = 0;
            }
            if (count($winLPos) == 0) {
                $nums = array();
                for ($k = 0; $k < $cls * $rws; $k++) {
                    array_push($nums, rand(0, $max - 1));
                }
            } else {
                break;
            }
        }
        $free = 0;
        $dclty = env('SLOT_FREE_DIFFICULTY');
        $canReward = rand(0, 5 * ($dclty + 1)) == 2 * ($dclty + 1);
        if ($canReward) {
            $qty = rand(0, 3 * ($dclty + 1)) == 1 * ($dclty + 1) ? 2 : 1;
            $userDb->increment('free', $qty);
            if (count($winLPos) == 0) {
                $aPos = rand(0, count($nums) -1);
                array_push($winLPos, $aPos);
                $nums[$aPos] = $max;
                if ($qty > 1) {
                    $aPos2 = rand(0, count($nums) -1);
                    while ($aPos2 == $aPos) {
                        $aPos2 = rand(0, count($nums) -1);
                    }
                    array_push($winLPos, $aPos2);
                    $nums[$aPos2] = $max;
                }
                $free = $qty;
            } else {
                $again = true;
                while ($qty > 0) {
                    $aPos = rand(0, count($nums) -1);
                    if (!in_array($aPos, $winLPos)) {
                        array_push($winLPos, $aPos);
                        $nums[$aPos] = $max;
                        $qty -= 1;
                        $free += 1;
                    }
                }
            }
        }
        $card = '';
        $dclty = env('SLOT_CARD_DIFFICULTY');
        $canReward = rand(0, 5 * ($dclty + 1)) == 2 * ($dclty + 1);
        if ($canReward) {
            $qty = rand(0, 3 * ($dclty + 1)) == 1 * ($dclty + 1) ? 2 : 1;
            if ($qty == 1) {
                $cid = Funcs::addCard($user->userid, env('SLOT_CARD_ID'), 1);
                $cd = DB::table('scratcher_game')->where('id', env('SLOT_CARD_ID'))->first();
                $card = $cd->name.'@@'.$cd->image.'@@'.$cd->coord.'@@'.$cid;
            } else {
                Funcs::addCard($user->userid, env('SLOT_CARD_ID'), 2);
                $card = 'm-'.env('SLOT_CARD_ID');
            }
            if (count($winLPos) == 0) {
                $aPos = rand(0, count($nums) -1);
                array_push($winLPos, $aPos);
                $nums[$aPos] = $max + 1;
                if ($qty > 1) {
                    $aPos2 = rand(0, count($nums) -1);
                    while ($aPos2 == $aPos) {
                        $aPos2 = rand(0, count($nums) -1);
                    }
                    array_push($winLPos, $aPos2);
                    $nums[$aPos2] = $max + 1;
                }
            } else {
                $again = true;
                while ($qty > 0) {
                    $aPos = rand(0, count($nums) -1);
                    if (!in_array($aPos, $winLPos)) {
                        array_push($winLPos, $aPos);
                        $nums[$aPos] = $max + 1;
                        $qty -= 1;
                    }
                }
            }
        }
        if ($userData->free > 0) {
            $user->increment('balance', $won);
            $userDb->decrement('free', 1);
            if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                Funcs::leaderboard($user->name, $user->userid, $won);
            }
        } else {
            $user->increment('balance', $won - $cost);
            Funcs::deductGamePoints($user, 'Slot', $cost);
            $userDb->increment('round', 1);
            if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                Funcs::leaderboard($user->name, $user->userid, $won - $cost);
            }
        }
        DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'Slot'], ['points' => DB::raw("points + '$won'")]);
        return [
            'status' => 1,
            'b' => $user->balance,
            'w' => $won,
            'l' => implode(",", $lines),
            'r' => implode(",", $winLPos),
            'i' => implode(",", $nums),
            'f' => $free,
            'c' => $card
        ];
    }
}
