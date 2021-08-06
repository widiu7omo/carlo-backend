<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Funcs;
use Cache;
use DB;

class Lotto extends Controller
{
    private function colToSelect($date1, $date2)
    {
        $lastDMY = date("d-m-Y", strtotime('-1 days'));
        if ($lastDMY == $date1) {
            return '2';
        } else {
            return '1';
        }
    }

    public function get(Request $req)
    {
        $user = $req['user'];
        $remain = env('GAME_LOTTO_DAILY');
        $check = DB::table('lotto_player')->where('userid', $user->userid)->first();
        if ($check) {
            $col = $this->colToSelect($check->lotto_date_1, $check->lotto_date_2);
            if ($check->{'lotto_date_'.$col} == date("d-m-Y")) {
                $remain -= count(array_filter(explode(',', $check->{'lotto_data_'.$col})));
            }
        }
        Funcs::makeLottoResult();
        return [
            'status' => 1,
            'balance' => $user->balance,
            'cost' => env('GAME_LOTTO_COST'),
            'chances' => $remain,
            'winner' => Funcs::getmisc('lotto_winner'),
            'next_draw' => \Carbon\Carbon::tomorrow()->timestamp * 1000
        ];
    }

    public function post(Request $req)
    {
        $user = $req['user'];
        $cost = env('GAME_LOTTO_COST');
        if ($user->balance < $cost) {
            return ['status' => '0', 'message' => 'Insufficient balance!'];
        }
        $check = DB::table('lotto_player')->where('userid', $user->userid);
        $checkData = $check->first();
        $date = date("d-m-Y");
        $data = $req->get('n');
        if (strlen($data) != 10 || !is_numeric($data)) {
            return ['status' => '0', 'message' => 'Enter 5 sets of numbers!'];
        }
        if ($checkData) {
            $col = $this->colToSelect($checkData->lotto_date_1, $checkData->lotto_date_2);
            $dte = 'lotto_date_'.$col;
            $dta = 'lotto_data_'.$col;
            if ($checkData->$dte == $date) {
                $count = count(array_filter(explode(',', $checkData->$dta))) +1;
                if ($count > env('GAME_LOTTO_DAILY')) {
                    return ['status' => '0', "message" => 'Daily limit exceeded!'];
                } else {
                    Funcs::deductGamePoints($user, 'Lotto', $cost);
                    $check->update([$dte => $date, $dta => $checkData->$dta . ',' . $data]);
                }
            } else {
                Funcs::deductGamePoints($user, 'Lotto', $cost);
                $check->update([$dte => $date, $dta => $data]);
            }
        } else {
            Funcs::deductGamePoints($user, 'Lotto', $cost);
            DB::table('lotto_player')->insert(['userid' => $user->userid, 'lotto_data_1' => $data, 'lotto_date_1' => $date]);
        }
        return ['status' => '1', 'message' => "Your chosen 5 sets of numbers added for the next draw"];
    }

    public function history(Request $req)
    {
        $user = $req['user'];
        $response = array();
        $table = DB::table('lotto_player')->where('userid', $user->userid);
        $data =  $table->first();
        $today = date("d-m-Y");
        $yesterday = date("d-m-Y", strtotime('-1 days'));
        $todayHistory = '';
        $yesterdayHistory = array();
        $canClaim = false;
        if ($data) {
            $yesterdayExist = false;
            if ($data->lotto_date_1 == $today) {
                $todayHistory = $data->lotto_data_1;
            } elseif ($data->lotto_date_1 == $yesterday) {
                $yesterdayExist = true;
            }
            if ($data->lotto_date_2 == $today) {
                $todayHistory = $data->lotto_data_2;
            } elseif ($data->lotto_date_2 == $yesterday) {
                $yesterdayExist = true;
            }
            if ($yesterdayExist) {
                $canClaim = $data->lotto_rewarded != $yesterday;
            }
        }
        $cacheKey = 'lotto_y'.$user->userid;
        if ($canClaim) {
            Cache::forget($cacheKey);
        }
        $yesterdayHistory = Cache::remember($cacheKey, 10080, function () use ($data, $yesterday) {
            $hist = array();
            if ($data) {
                if ($data->lotto_date_1 == $yesterday) {
                    $hist = explode(',', $data->lotto_data_1);
                } elseif ($data->lotto_date_2 == $yesterday) {
                    $hist = explode(',', $data->lotto_data_2);
                }
            }
            $claimPoints = 0;
            $winnerdigits = str_split(Funcs::getmisc('lotto_winner'), 2);
            for ($i = 0; $i < count($hist); $i++) {
                $userdigits = str_split($hist[$i], 2);
                $count = count(array_intersect($winnerdigits, $userdigits));
                $amt = env('GAME_LOTTO_MATCH_' . $count);
                $hist[$i] = ['number' => $hist[$i], 'count' => $count, 'reward' => $amt['amount']];
                $claimPoints += $amt['amount'];
            }
            return ['points' => $claimPoints, "hist" => $hist, "count" => count($hist)];
        });
        if ($canClaim) {
            $table->update(['lotto_rewarded' => $yesterday]);
            $p = $yesterdayHistory['points'];
            $user->increment('balance', $p);
            if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                Funcs::leaderboard($user->name, $user->userid, $p);
            }
            DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'Lotto'], ['points' => DB::raw("points + '$p'")]);
            //$cost = $yesterdayHistory['count'] * env('GAME_LOTTO_COST');
                //DB::table('game_lotto_history')->insert(['userid' => $user->userid, 'spent' => $cost, 'rewarded' => $p]);
        }
        return ['status' => '1', 'balance' => $user->balance, 'today' => $todayHistory, 'yesterday' => $yesterdayHistory['hist']];
    }
}
