<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use \Exception;
use Funcs;
use Cache;
use DB;

class GuessWord extends Controller
{
    public function info(Request $req)
    {
        $user = $req['user'];
        $check = DB::table('guess_word_player')->where('userid', $user->userid)->first();
        if ($check) {
            $rc = (int) env('GW_RETRY_CHANCE') - $check->retry;
            $hc = (int) env('GW_HINT_CHANCE') - $check->hint;
        } else {
            $rc = (int) env('GW_RETRY_CHANCE');
            $hc = (int) env('GW_HINT_CHANCE');
        }
        $leaderboard = Funcs::rank($user->userid);
        return [
            'status' => 1,
            'rank' => $leaderboard['rank'],
            'score' => $leaderboard['score'],
            'retry_cost' => env('GW_RETRY_COST'),
            'retry_chance' => $rc,
            'hint_cost' => env('GW_HINT_COST'),
            'hint_chance' => $hc,
            'solve_cost' => env('GW_SOLVE_COST')
        ];
    }
    public function get(Request $req)
    {
        $user = $req['user'];
        $cc = $req->get('cc') == null ? 'all' : $req->get('cc');
        $db = DB::table('guess_word_player')->where('userid', $user->userid);
        $check = $db->first();
        if ($check) {
            if ($check->retry == (int) env('GW_RETRY_CHANCE')) {
                return ['status' => 10, 'message' => 'Not enough chances available to try. You need to purchase retry chance.'];
            }
            $qs = DB::table('guess_word')
                    ->where(function ($query) use ($cc) {
                        return $query
                            ->where('country', 'LIKE', '%'.$cc.'%')
                            ->orWhere('country', 'all');
                    })
                    ->where('id', '>', $check->last_id)
                    ->first();
            if ($qs) {
                if (Carbon::parse($check->updated_at)->isToday()) {
                    $db->update([
                        'last_id' => $qs->id,
                        'word' => $qs->word,
                        'rewarded' => 0,
                        'updated_at' => Carbon::now()
                    ]);
                } else {
                    $db->update([
                        'last_id' => $qs->id,
                        'word' => $qs->word,
                        'rewarded' => 0,
                        'retry' => 0,
                        'hint' => 0,
                        'updated_at' => Carbon::now()
                    ]);
                }
                $letters = str_split($qs->word);
                shuffle($letters);
                return [
                    'status' => 1,
                    'image' => $qs->image,
                    'info' => $qs->info,
                    'timeup' => $qs->max_time * 1000,
                    'word' => $letters
                ];
            } else {
                return ['status' => 2, 'reason' => 2, 'message' => 'Not enough questions available for you. Try again later.'];
            }
        } else {
            $qs = DB::table('guess_word')
                    ->where(function ($query) use ($cc) {
                        return $query
                            ->where('country', 'LIKE', '%'.$cc.'%')
                            ->orWhere('country', 'all');
                    })
                    ->first();
            if ($qs) {
                DB::table('guess_word_player')->insert([
                    'userid' => $user->userid,
                    'last_id' => $qs->id,
                    'word' => $qs->word,
                    'updated_at' => Carbon::now()
                ]);
                $letters = str_split($qs->word);
                shuffle($letters);
                return [
                    'status' => 1,
                    'image' => $qs->image,
                    'info' => $qs->info,
                    'timeup' => $qs->max_time * 1000,
                    'word' => $letters
                ];
            } else {
                return ['status' => 2, 'reason' => 2, 'message' => 'Not enough questions available for you. Try again later.'];
            }
        }
    }

    public function reward(Request $req)
    {
        $user = $req['user'];
        $reply = $req->get('r');
        $gwData = DB::table('guess_word_player')->where('userid', $user->userid);
        $check = $gwData->first();
        if ($check) {
            if ($check->rewarded == 1) {
                return ['status' => 2, 'reason' => 1, 'message' => 'You already got the result for this puzzle!'];
            }
            if ($check->retry == (int) env('GW_RETRY_CHANCE')) {
                return ['status' => 2, 'reason' => 1, 'message' => 'Not enough chances available to try. You need to purchase retry chance.'];
            }
            $puzzle = DB::table('guess_word')->where('id', $check->last_id)->first();
            if ($puzzle) {
                $diff = (int) Carbon::now()->diffInSeconds(Carbon::parse($check->updated_at));
                if ($diff > $puzzle->max_time + env('GW_TIME_OFFSET')) {
                    return ['status' => 2, 'reason' => 1, 'message' => 'Times up!'];
                }
                if (strtoupper($reply) == $check->word) {
                    $gwData->update(['rewarded' => 1]);
                    $amt = (int) env('GW_REWARD');
                    DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'GuessWord'], ['points' => DB::raw("points + '$amt'")]);
                    Funcs::leaderboard($user->name, $user->userid, $amt);
                    return ['status' => 1, 'message' => $amt];
                } else {
                    $gwData->increment('retry', 1);
                    return ['status' => 2, 'reason' => 2, 'message' => 'Wrong answer!'];
                }
            }
        }
        return ['status' => 2, 'reason' => 0,'message' => 'Puzzle not found!'];
    }

    public function hint(Request $req)
    {
        $user = $req['user'];
        $rL = $req->get('l');
        $db = DB::table('guess_word_player')->where('userid', $user->userid);
        $check = $db->first();
        if ($check && is_numeric($rL)) {
            if ($check->rewarded == 1) {
                return ['status' => 0, 'message' => 'You already got the result for this puzzle!'];
            }
            if ($check->hint < (int) env('GW_HINT_CHANCE')) {
                $letter = str_split($check->word)[$rL];
                $db->increment('hint', 1);
                return ['status' => 1, 'message' => $letter];
            } else {
                return ['status' => 0, 'message' => 'Purchase hint chances!'];
            }
        } else {
            return ['status' => 0, 'message' => 'Invalid letter request!'];
        }
    }

    public function solve(Request $req)
    {
        $user = $req['user'];
        $db = DB::table('guess_word_player')->where('userid', $user->userid);
        $check = $db->first();
        if ($check) {
            if ($check->rewarded == 1) {
                return ['status' => 0, 'message' => 'You already got the result for this puzzle!'];
            }
            $cost = (int) env('GW_SOLVE_COST');
            if ($user->balance < $cost) {
                return ['status' => 10, 'message' => 'Insufficient balance!'];
            }
            Funcs::deductGamePoints($user, 'GuessWord', $cost);
            $db->update(['rewarded' => 1]);
            return ['status' => 1, 'word' => str_split($check->word)];
        } else {
            return ['status' => 0, 'message' => 'Invalid letter request!'];
        }
    }
}
