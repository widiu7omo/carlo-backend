<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use \Exception;
use Cache;
use DB;

class Quiz extends Controller
{
    public function Categories(Request $req)
    {
        $user = $req['user'];
        $leaderboard = \Funcs::rank($user->userid);
        return ['status' => 1, 'rank' => $leaderboard['rank'], 'score' => $leaderboard['score'], 'quiz' => DB::table('quiz_category')->get()];
    }

    public function catInfo(Request $req)
    {
        $user = $req['user'];
        $q = DB::table('quiz_category')->where('id', $req->get('c'))->first();
        $wrong = 0;
        $check = DB::table('quiz_player')->where('userid', $user->userid)->first();
        if ($check) {
            if (Carbon::parse($check->updated_at)->isToday()) {
                $wrong = $check->wrong;
            }
        }
        $lm = env('QUIZ_WRONG_LIMIT');
        return [
            'status' => 1,
            'title' => $q->title,
            'desc' => $q->description,
            'grace' => (int) $lm - $wrong,
            'grace_limit' => $lm,
            'reward_per' => $q->reward,
            'round_cost' => (int) env('QUIZ_ROUND_COST'),
            'fifty_cost' => (int) env('QUIZ_FIFTY_COST'),
            'skip_cost' => (int) env('QUIZ_SKIP_COST')
        ];
    }

    public function quiz(Request $req)
    {
        $c_id = (string) $req->get('c');
        if ($c_id == null) {
            return ['status' => 0, 'message' => 'Invalid category selected'];
        }
        $cat = DB::table('quiz_category')->where('id', (int) $c_id)->first();
        if ($cat) {
            $user = $req['user'];
            $quizM = DB::table('quiz_player')->where('userid', $user->userid);
            $check = $quizM->first();
            $wrong = 0;
            $isPremium = 0;
            if ($check) {
                $isToday = Carbon::parse($check->updated_at)->isToday();
                if ($isToday) {
                    $skip = (string) $req->get('p');
                    if ($skip != null) {
                        $deduct = (int) env('QUIZ_SKIP_COST');
                        if ($user->balance < $deduct) {
                            return ['status' => 10, 'message' => 'Not enough balance to perform this action!'];
                        }
                        Funcs::deductGamePoints($user, 'Quiz', $deduct);
                        $wrong = $check->wrong;
                    } elseif ($check->rewarded == 0) {
                        $wrong = $check->wrong + 1;
                        $quizM->update(['rewarded' => 1,'wrong' => $wrong]);
                    } else {
                        $wrong = $check->wrong;
                    }
                    $wrongLimit = env('QUIZ_WRONG_LIMIT');
                    if ($wrongLimit <= $wrong) {
                        return ['status' => 2, 'reason' => 1, 'message' => 'Try again tomorrow or purchase a new round'];
                    }
                }
            }
            $cc = $req->get('cc') == null ? 'all' : $req->get('cc');
            $q = DB::table('quiz')
                    ->where('category', $c_id)
                    ->where(function ($query) use ($cc) {
                        return $query
                            ->where('country', 'LIKE', '%'.$cc.'%')
                            ->orWhere('country', 'all');
                    })
                    ->inRandomOrder()
                    ->first();
            if (!$q) {
                return ['status' => 2, 'reason' => 0, 'message' => 'Not enough questions available for you. Try again later.'];
            }
            $qs = $q->question;
            if ($c_id == 1) {
                $func = $q->functions;
                $func_array = explode("; ", $func);
                $new_func = '';
                foreach ($func_array as $f) {
                    if (strpos($f, '$function') === false && strpos($f, '$result') === false) {
                        $f_v = explode('=', str_replace([' ',"\n","\r"], '', $f));
                        $fn = $f_v[0];
                        $v = (int) $f_v[1];
                        $new_v = $v + rand(10, 100);
                        $qs = str_replace($fn, $new_v, $qs);
                        $new_func .= str_replace($v, $new_v.';', $f);
                    } else {
                        $new_func .= $f.';';
                    }
                }
                eval($new_func);
                if (strpos($function, '.') !== false) {
                    $function = (float)number_format((float)$function, 2, '.', '');
                }
                $res = array($function);
                for ($i = 0; $i < 3; $i++) {
                    $r = rand($function - 10, $function + 50);
                    if (strpos($function, '.') !== false) {
                        $r .= '.'.rand(0, 99);
                        array_push($res, (float) $r);
                    } else {
                        array_push($res, $r);
                    }
                }
                $res = array_unique($res);
                shuffle($res);
                $ans = array_search($function, $res);
            } else {
                $res = explode('||', $q->functions);
                $svd = $res[$q->answer - 1];
                shuffle($res);
                $ans = array_search($svd, $res);
            }
            $now = Carbon::now();
            if ($check) {
                $quizM->update([
                    'category' => $c_id,
                    'o_count' => count($res) - 1,
                    'answer' => $ans,
                    'rewarded' => 0,
                    'wrong' => $wrong,
                    'fifty' => 0,
                    'updated_at' => $now
                ]);
            } else {
                DB::table('quiz_player')->insert([
                    'userid' => $user->userid,
                    'category' => $c_id,
                    'o_count' => count($res) - 1,
                    'answer' => $ans,
                    'rewarded' => 0,
                    'wrong' => 0,
                    'fifty' => 0,
                    'updated_at' => $now
                ]);
            }
            return ['status' => 1, 'timeup' => $cat->quiz_time * 1000, 'q' => $qs, 'o' => $res];
        } else {
            return ['status' => 2, 'reason' => 0, 'message' => 'Invalid category selected'];
        }
    }

    public function reward(Request $req)
    {
        try {
            $user = $req['user'];
            $c_id = (int) $req->get('c');
            $r = $req->get('r');
            if ($c_id == null || $r == null) {
                return ['status' => 0, 'message' => 'Invalid category or result type!'];
            }
            $cat = DB::table('quiz_category')->where('id', $c_id)->first();
            if ($cat) {
                $db = DB::table('quiz_player')->where('userid', $user->userid);
                $check = $db->first();
                if ($check) {
                    if ($check->rewarded == 1) {
                        return ['status' => 0, 'message' => 'Credit already adjusted for this question.'];
                    } else {
                        $db->update(['rewarded' => 1]);
                    }
                    $wrong = $check->wrong;
                    $grace = (int) env('QUIZ_WRONG_LIMIT') - $wrong;
                    if ($c_id != $check->category) {
                        $db->update(['wrong' => $wrong + 1]);
                        return ['status' => 2, 'grace' => $grace - 1,'message' => 'The answer is not belong to this category!'];
                    }
                    $diff = (int) Carbon::now()->diffInSeconds(Carbon::parse($check->updated_at));
                    if ($diff > $cat->quiz_time + env('QUIZ_TIME_OFFSET')) {
                        $db->update(['wrong' => $wrong + 1]);
                        return ['status' => 2, 'grace' => $grace - 1, 'message' => 'Times up!'];
                    }
                    if ($r == $check->answer) {
                        DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'Quiz'], ['points' => DB::raw("points + '$cat->reward'")]);
                        \Funcs::leaderboard($user->name, $user->userid, $cat->reward);
                        return ['status' => 1, 'is_correct' => 1, 'grace' => $grace, 'ans' => $check->answer];
                    } else {
                        $db->update(['wrong' => $wrong + 1]);
                        return ['status' => 1, 'is_correct' => 0, 'grace' => $grace - 1, 'ans' => $check->answer];
                    }
                } else {
                    return ['status' => 0, 'message' => 'User data not found'];
                }
            } else {
                return ['status' => 0, 'message' => 'Invalid category selected'];
            }
        } catch (Exception $e){
            return ['status' => -9, 'message' => $e->getMessage()];
        }
    }

    public function purchase(Request $req)
    {
        $user = $req['user'];
        $cost = env('QUIZ_ROUND_COST');
        if ($cost > $user->balance) {
            return ['status' => 10, 'message' => 'Insufficient balance'];
        }
        $db = DB::table('quiz_player')->where('userid', $user->userid);
        $check = $db->first();
        if ($check) {
            $limit = env('QUIZ_WRONG_LIMIT');
            if ($check->wrong < $limit) {
                return ['status' => 0, 'message' => 'You can try ' . ($limit - $check->wrong) . ' more times before purchase a new round.'];
            }
            Funcs::deductGamePoints($user, 'Quiz', $cost);
            $db->update(['wrong' => 0]);
            return ['status' => 1, 'grace' => $limit, 'message' => 'You have successfully purchased a new round.'];
        } else {
            return ['status' => 0, 'message' => 'You do not have to purchase any round yet!'];
        }
    }

    public function Fifty(Request $req)
    {
        $user = $req['user'];
        $cost = (int) env('QUIZ_FIFTY_COST');
        if ($user->balance < $cost) {
            return ['status' => 10, 'message' => 'Not enough balance to perform this action!'];
        }
        $db = DB::table('quiz_player')->where('userid', $user->userid);
        $quizData = $db->first();
        if ($quizData) {
            $c = $req->get('c');
            if ($c == null || $c != $quizData->category || $quizData->rewarded == 1) {
                return ['status' => 2, 'message' => 'Invalid category!'];
            }
            if ($quizData->fifty == 0) {
                $half = round($quizData->o_count / 2);
                $numbers = range(0, $quizData->o_count);
                shuffle($numbers);
                $o = array();
                for ($i = 0; $i < $half; $i++) {
                    $n = $numbers[$i];
                    if ($n == $quizData->answer) {
                        array_push($o, $numbers[count($numbers) -1]);
                    } else {
                        array_push($o, $n);
                    }
                }
                Funcs::deductGamePoints($user, 'Quiz', $cost);
                $db->update(['fifty'=> 1]);
                return ['status' => 1, 'o' => $o];
            } else {
                return ['status' => 2, 'message' => 'Already used this option!'];
            }
        } else {
            return ['status' => 2, 'message' => 'Data not found!'];
        }
    }
}
