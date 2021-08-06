<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Exception;
use Cache;
use DB;

class GameTour extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $begin = env('TOUR_BEGIN_TIME');
        if ($begin == 0) {
            $begin = "hh:mm dd/mm/yyyy";
        } else {
            $begin = Carbon::createFromTimestamp($begin)->format('H:i d/m/Y');
        }
        $s = [
            'name' => str_replace('_', ' ', env('TOUR_NAME')),
            'begin_time' => $begin,
            'publish_time' => env('TOUR_PUB_TIME'),
            'result_time' => Carbon::createFromTimestamp(env('TOUR_RESULT_TIME'))->format('H:i d/m/Y'),
            'fee' => env('TOUR_ENTRY_FEE'),
            'reward' => env('TOUR_REWARD')
        ];
        $data=['s' => $s, 'q' => DB::table('tour_question')->paginate(6)];
        return view('admin.game_tournament', compact('data'));
    }

    public function sett(Request $req)
    {
        $this->validate($req, [
            'name' => 'required|string|max:20',
            'begin_time' => 'required|string',
            'publish_time' => 'required|integer|min:10',
            'result_time' => 'required|string',
            'begin_time' => 'required|string',
            'fee' => 'required|integer|min:0',
            'reward' => 'required|integer|gt:fee'
        ]);
        try {
            $beginTs = Carbon::createFromFormat('H:i d/m/Y', $req->post('begin_time'))->timestamp;
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Problem with TOURNAMENT BEGINNING TIME: '.$e->getMessage());
        };
        try {
            $resultTs = Carbon::createFromFormat('H:i d/m/Y', $req->post('result_time'))->timestamp;
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Problem with RESULT SHOWING TIME: '.$e->getMessage());
        };
        $nowTs = Carbon::now()->timestamp;
        if ($beginTs < $nowTs) {
            return back()->withInput()->with('error', 'Beginning time cannot be earlier than current time!');
        }
        if ($beginTs > $nowTs + 5184000) {
            return back()->withInput()->with('error', 'Schedule your begining time in 60 days from today!');
        }
        if ($resultTs < $beginTs + 21600) {
            return back()->withInput()->with('error', 'Result displaying time must be longer than 6 hours since the tournament beginning time.');
        }
        $pubTs = $req->get('publish_time');
        if ($resultTs < $beginTs + $pubTs * 60 + 3600) {
            return back()->withInput()->with('error', 'Either increase your RESULT DISPLAYING TIME or lower your RESULT PUBLISHING TIME.');
        }
        if ($resultTs != env('TOUR_RESULT_TIME')) {
            DB::table('tour_player')->truncate();
            Cache::forget('tour_rank');
            Cache::forget('tour_rwd');
            \Funcs::setEnv('TOUR_RES_PUBLISHED', 0);
        }
        \Funcs::setEnv('TOUR_NAME', preg_replace('/\s+/', '_', $req->post('name')), false);
        \Funcs::setEnv('TOUR_BEGIN_TIME', $beginTs, false);
        \Funcs::setEnv('TOUR_PUB_TIME', $pubTs, false);
        \Funcs::setEnv('TOUR_RESULT_TIME', $resultTs, false);
        \Funcs::setEnv('TOUR_ENTRY_FEE', $req->post('fee'), false);
        \Funcs::setEnv('TOUR_REWARD', $req->post('reward'));
        Cache::forget('connect_tour');
        Cache::forget('tour_q');
        return back()->with('success', 'Settings updated');
    }

    protected function str_ordinal($value)
    {
        $number = abs($value);
        $indicators = ['th','st','nd','rd','th','th','th','th','th','th'];
        $suffix = $indicators[$number % 10];
        if ($number % 100 >= 11 && $number % 100 <= 13) {
            $suffix = 'th';
        }
        return number_format($number) . $suffix;
    }

    public function winnerForm(Request $req)
    {
        $this->validate($req, ['total_winners' => 'required|integer|min:3|max:10']);
        $counts = $req->post('total_winners');
        $inputs = array();
        $stored = explode(',', env('TOUR_WINNER_PCT'));
        for ($i = 1; $i < $counts + 1; $i++) {
            $aa = $this->str_ordinal($i);
            array_push($inputs, ['name' => $aa.' winner', 'param' => 'winner_'.$i, 'pct' => isset($stored[$i-1]) ? $stored[$i-1] : 0]);
        }
        $data = ['c' => $counts, 'd' => $inputs];
        return view('admin.game_tournament_w', compact('data'));
    }

    public function winnerUpdate(Request $req)
    {
        $this->validate($req, [
            's' => 'required|integer|min:3|max:20',
            'winner_3' => 'required|integer|min:1',
            'winner_2' => 'required|integer|gt:winner_3',
            'winner_1' => 'required|integer|gt:winner_2',
        ]);
        $d = '';
        $c = 0;
        $count = 0;
        for ($i = 0; $i < $req->post('s'); $i++) {
            $a = $req->post('winner_'.($i+1));
            if (is_numeric($a)) {
                $c += $a;
                if ($a != 0) {
                    $d .= $a.',';
                    $count += 1;
                }
            }
        }
        if ($c == 100) {
            \Funcs::setEnv('TOUR_WINNER_COUNT', $count);
            \Funcs::setEnv('TOUR_WINNER_PCT', rtrim($d, ','));
            return redirect()->route('game_tour')->with('success', 'Reward distribution updated successfully.');
        } else {
            return back()->withInput()->with('error', 'Summation of '.$d.' is equal to '.$c.'. It has to be 100.');
        }
    }

    public function qsAdd(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|integer|min:-1',
            'question' => 'required|string',
            'options' => 'required|string',
            'answer' => 'required|integer|min:1|max:10',
            'time' => 'required|integer|min:5',
            'score' => 'required|integer|min:1'
        ]);
        $id = $req->post('id');
        $func = $req->post('options');
        $qs = $req->post('question');
        $ans = $req->post('answer');
        $time = $req->post('time');
        $sc = $req->post('score');
        try {
            $fn = str_replace(PHP_EOL, ";;", $func);
            $lines = explode(";;", $fn);
            $resultLine = count($lines);
            $newLines = '';
            for ($i = 0; $i < $resultLine; $i++) {
                if ($lines[$i] != null || $lines[$i] != '') {
                    $newLines .= $lines[$i].';;';
                }
            }
            $newLines = implode(";;", array_filter(explode(";;", $newLines)));
            if ($ans < 1 || $ans > $resultLine) {
                return back()->withInput()->with('error', 'Enter a correct answer line number');
            }
            if ($id == -1) {
                DB::table('tour_question')->insert(['question' => $qs, 'options' => $newLines, 'answer' => $ans, 'time' => $time, 'score' => $sc]);
                return back()->with('success', 'Question added successfully');
            } else {
                DB::table('tour_question')->where('id', $id)->update(['question' => $qs, 'options' => $newLines, 'answer' => $ans, 'time' => $time, 'score' => $sc]);
                return back()->with('success', 'Question updated');
            }
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        };
    }

    public function qsDel(Request $req)
    {
        $id = $req->post('id');
        if ($id == "-1") {
            DB::table('tour_question')->truncate();
            return back()->with('success', 'All questions deleted');
        } else {
            DB::table('tour_question')->where('id', $id)->delete();
            return back()->with('success', 'Question deleted');
        }
    }
}
