<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Exception;
use Cache;
use DB;

class GameQuiz extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function quizCategory(Request $req)
    {
        $data = DB::table('quiz_category')->orderBy('id', 'desc')->paginate(6);
        return view('admin.game_quiz_category', compact('data'));
    }

    public function updateSettings(Request $req)
    {
        $this->validate($req, [
                'time' => 'required|integer|min:0',
                'incorrect' => 'required|integer|min:1',
                'cost' => 'required|integer|min:1',
                'fifty' => 'required|integer|min:0',
                'skip' => 'required|integer|min:0'
            ]);
        $offset = (int) $req->post('time');
        $incorrect = (int) $req->post('incorrect');
        $cost = (int) $req->post('cost');
        $fifty = (int) $req->post('fifty');
        $skip = (int) $req->post('skip');
        \Funcs::setEnv('QUIZ_TIME_OFFSET', $offset, false);
        \Funcs::setEnv('QUIZ_WRONG_LIMIT', $incorrect, false);
        \Funcs::setEnv('QUIZ_ROUND_COST', $cost, false);
        \Funcs::setEnv('QUIZ_FIFTY_COST', $fifty, false);
        \Funcs::setEnv('QUIZ_SKIP_COST', $skip);
        return back()->with('success', 'Settings updated');
    }

    public function quizAddCategory(Request $req)
    {
        $this->validate($req, [
            'quiz_category_name' => 'required|string|max:79',
            'quiz_category_description' => 'required|string|max:189',
            'quiz_category_time' => 'required|integer|min:5',
            'quiz_card_image' => 'required|mimes:jpeg,jpg,png|max:1000',
        ]);
        try {
            $path = public_path('uploads');
            $filename = Carbon::now()->timestamp.'.'.$req->file('quiz_card_image')->getClientOriginalExtension();
            $req->file('quiz_card_image')->move($path, $filename);
            DB::table('quiz_category')->insert([
                'title' => $req->post('quiz_category_name'),
                'description' => $req->post('quiz_category_description'),
                'quiz_time' => $req->post('quiz_category_time'),
                'image' => env('APP_URL').'/public/uploads/'.$filename
            ]);
            return back()->with('success', 'Category added successfully');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function quizEditCategory(Request $req)
    {
        $id = (int) $req->post('id');
        $c = DB::table('quiz_category')->where('id', $id);
        $check = $c->first();
        if ($check) {
            $this->validate($req, [
                'update_category_name' => 'required|string|max:79',
                'update_category_description' => 'required|string|max:189',
                'update_category_time' => 'required|integer|min:5',
                'update_card_image' => 'nullable|mimes:jpeg,jpg,png|max:1000'
            ]);
            try {
                $filename = basename($check->image);
                if ($req->hasFile('update_card_image')) {
                    $path = public_path('uploads');
                    if ($filename != null && file_exists($path.'/'.$filename)) {
                        unlink($path.'/'.$filename);
                    };
                    $filename = Carbon::now()->timestamp.'.'.$req->file('update_card_image')->getClientOriginalExtension();
                    $req->file('update_card_image')->move($path, $filename);
                }
                $c->update([
                    'title' => $req->post('update_category_name'),
                    'quiz_time' => $req->post('update_category_time'),
                    'image' => env('APP_URL').'/public/uploads/'.$filename,
                    'description' => $req->post('update_category_description')
                ]);
                return back()->with('success', 'Category added successfully');
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        } else {
            return back()->with('error', "Category not found!");
        }
    }

    public function quizDelCategory(Request $req)
    {
        $id = (int) $req->post('id');
        if ($id == 1) {
            return back()->with('error', 'This category cannot be deleted!');
        } else {
            $c = DB::table('quiz_category')->where('id', $id);
            if ($c->first()) {
                $filename = basename($c->first()->image);
                $path = public_path('uploads');
                if ($filename != null && file_exists($path.'/'.$filename)) {
                    unlink($path.'/'.$filename);
                };
                $c->delete();
                DB::table('quiz')->where('category', $id)->delete();
                return back()->with('success', 'Successfully removed selected category and all of its questions');
            }
        }
    }


    public function quizView(Request $req)
    {
        $id = $req->get('id');
        $check = DB::table('quiz_category')->where('id', $id)->first();
        if (!$check) {
            return back()->with('error', 'Invalid category selected');
        }
        $data = array('id' => $id);
        if ($id == 1) {
            $data['header_title'] = 'Add a Math quiz';
            $data['placeholder_question'] = 'If we add $a amount with $b then what will be the result?';
            $data['function_title'] = 'Enter your function and result';
            $data['placeholder_function'] = '$a = 10&#10;$b = 10&#10;$function = $a + $b&#10;$result = 20';
        } else {
            $data['header_title'] = 'Add a quiz';
            $data['placeholder_question'] = 'What is the capital of United States?';
            $data['function_title'] = 'Enter your options and answer';
            $data['placeholder_function'] = 'California&#10;New York&#10;Washington, D.C.&#10;$answer = 3';
        }
        $data['cat'] = $check->title;
        $data['time'] = $check->quiz_time;
        $data['desc'] = $check->description;
        $data['q'] = DB::table('quiz')->where('category', $id)->orderBy('id', 'desc')->paginate(10);
        return view('admin.game_quiz', compact('data'));
    }

    public function quizAdd(Request $req)
    {
        $qid = (int) $req->post('id');
        $func = $req->post('quiz_function');
        $qs = $req->post('quiz_question');
        if ($qs == null) {
            return back()->withInput()->with('error', 'Enter your question.');
        }
        if ($func == null) {
            return back()->withInput()->with('error', 'Function field cannot be empty.');
        }
        $ctry = $req->post('quiz_country');
        if ($ctry == null) {
            $ctry = 'all';
        } else {
            $ct = explode(',', $ctry);
            $ol = DB::table('online_users')->pluck('country_iso');
            foreach ($ct as $c) {
                if (strlen($c) != 2) {
                    return back()->withInput()->with('error', 'Enter 2 digit country ISO code. For multiple countries make it comma seperated like: US,AU,GB,FR');
                } elseif (strpos($ol, strtoupper($c)) === false) {
                    return back()->withInput()->with('error', 'You have entered "'.$c.'" which is a wrong country ISO code. Enter a valid country ISO.');
                };
            }
        }
        if ($qid == 1) {
            try {
                if (strpos($func, '$function') !== false && strpos($func, '$result') !== false) {
                    eval($func);
                    $fn = str_replace(PHP_EOL, " ", $func);
                    if ($function == $result) {
                        DB::table('quiz')->insert([
                            'category' => 1,
                            'question' => $qs,
                            'functions' => $fn,
                            'country' => $ctry
                        ]);
                        return back()->with('success', 'Math Quiz added successfully');
                    } else {
                        return back()->withInput()->with('error', 'Make sure you created proper "$function" and the "$result" is correct output.');
                    }
                } else {
                    return back()->withInput()->with('error', '$function and $result variables must contain in text area!');
                };
            } catch (Exception $e) {
                return back()->withInput()->with('error', $e->getMessage());
            } catch (\ParseError $e) {
                return back()->withInput()->with('error', $e->getMessage());
            };
        } else {
            try {
                $fn = str_replace(PHP_EOL, "||", $func);
                $lines = explode("||", $fn);
                $resultLine = count($lines) - 1;
                $ans = 0;
                if (strpos($lines[$resultLine], '$answer') !== false) {
                    $a = explode('=', $lines[$resultLine]);
                    if (count($a) == 2) {
                        $ans = (int) $a[1];
                    }
                } else {
                    return back()->withInput()->with('error', 'Input a correct answer line number with the prefix "$answer=" in your last line.');
                }
                $newLines = '';
                for ($i = 0; $i < $resultLine; $i++) {
                    $newLines .= $lines[$i].'||';
                }
                $newLines = implode("||", array_filter(explode("||", $newLines)));
                if ($ans == 0 || $ans > $resultLine) {
                    return back()->withInput()->with('error', 'Input a correct answer line number with the prefix "$answer="');
                }
                DB::table('quiz')->insert([
                    'category' => $qid,
                    'question' => $qs,
                    'functions' => $newLines,
                    'answer' => $ans,
                    'country' => $ctry
                ]);
                return back()->with('success', 'General Quiz added successfully');
            } catch (Exception $e) {
                return back()->withInput()->with('error', $e->getMessage());
            };
        }
    }

    public function quizEdit(Request $req)
    {
        $id = (int) $req->post('id');
        $cz = DB::table('quiz')->where('id', $id);
        $check = $cz->first();
        if ($check) {
            $func = $req->post('update_function');
            $qs = $req->post('update_question');
            if ($qs == null) {
                return back()->withInput()->with('error', 'Enter your question.');
            }
            if ($func == null) {
                return back()->withInput()->with('error', 'Function field cannot be empty.');
            }
            $ctry = $req->post('update_country');
            if ($ctry == null) {
                $ctry = 'all';
            } else {
                $ct = explode(',', $ctry);
                $ol = DB::table('online_users')->pluck('country_iso');
                foreach ($ct as $c) {
                    if (strlen($c) != 2) {
                        return back()->withInput()->with('error', 'Enter 2 digit country ISO code. For multiple countries make it comma seperated like: US,AU,GB,FR');
                    } elseif (strpos($ol, strtoupper($c)) === false) {
                        return back()->withInput()->with('error', 'You have entered "'.$c.'" which is a wrong country ISO code. Enter a valid country ISO.');
                    };
                }
            }
            if ($check->category == 1) {
                try {
                    if (strpos($func, '$function') !== false && strpos($func, '$result') !== false) {
                        eval($func);
                        $fn = str_replace(PHP_EOL, " ", $func);
                        if ($function == $result) {
                            $cz->update([
                                'question' => $qs,
                                'functions' => $fn,
                                'country' => $ctry
                            ]);
                            return back()->with('success', 'Math Quiz added successfully');
                        } else {
                            return back()->withInput()->with('error', 'Make sure you created proper "$function" and the "$result" is correct output.');
                        }
                    } else {
                        return back()->withInput()->with('error', '$function and $result variables must contain in text area!');
                    };
                } catch (Exception $e) {
                    return back()->withInput()->with('error', $e->getMessage());
                } catch (\ParseError $e) {
                    return back()->withInput()->with('error', $e->getMessage());
                };
            } else {
                try {
                    $fn = str_replace(PHP_EOL, "||", $func);
                    $lines = explode("||", $fn);
                    $resultLine = count($lines) - 1;
                    $ans = 0;
                    if (strpos($lines[$resultLine], '$answer') !== false) {
                        $a = explode('=', $lines[$resultLine]);
                        if (count($a) == 2) {
                            $ans = (int) $a[1];
                        }
                    } else {
                        return back()->withInput()->with('error', 'Input a correct answer line number with the prefix "$answer=" in your last line.');
                    }
                    $newLines = '';
                    for ($i = 0; $i < $resultLine; $i++) {
                        $newLines .= $lines[$i].'||';
                    }
                    $newLines = implode("||", array_filter(explode("||", $newLines)));
                    if ($ans == 0 || $ans > $resultLine) {
                        return back()->withInput()->with('error', 'Input a correct answer line number with the prefix "$answer="');
                    }
                    $cz->update([
                        'question' => $qs,
                        'functions' => $newLines,
                        'answer' => $ans,
                        'country' => $ctry
                    ]);
                    return back()->with('success', 'General Quiz added successfully');
                } catch (Exception $e) {
                    return back()->withInput()->with('error', $e->getMessage());
                };
            }
        } else {
            return back()->with('error', "Quiz not found!");
        }
    }

    public function quizDel(Request $req)
    {
        try {
            DB::table('quiz')->where('id', $req->post('id'))->delete();
            return back()->with('success', 'Question deleted successfully');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
