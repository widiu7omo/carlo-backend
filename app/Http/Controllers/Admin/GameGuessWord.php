<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Funcs;
use DB;

class GameGuessWord extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $data = DB::table('guess_word')->orderBy('id', 'desc')->paginate(10);
        return view('admin.game_guess_word', compact('data'));
    }

    public function add(Request $req)
    {
        $this->validate($req, [
            'word_name' => ['required','string','max:50','regex:/([A-Za-z0-9\-]+)/', 'not_regex:/(\\?|\s)/'],
            'item_image' => 'nullable|mimes:jpeg,jpg,png|max:1000',
            'word_info' => 'required|string|max:190',
            'country' => 'nullable|string|max:190',
            'time' => 'required|integer|min:2'
        ]);
        $image = '';
        if ($req->hasFile('item_image')) {
            $path = public_path('uploads');
            $filename = Carbon::now()->timestamp.'.'.$req->file('item_image')->getClientOriginalExtension();
            $req->file('item_image')->move($path, $filename);
            $image = env('APP_URL').'/public/uploads/'.$filename;
        }
        $ctry = $req->post('country');
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
        DB::table('guess_word')->insert([
            'image' => $image,
            'info' => $req->post('word_info'),
            'word' => strtoupper($req->post('word_name')),
            'country' => strtolower($ctry),
            'max_time' => $req->post('time')
        ]);
        return back()->with('success', 'Item added');
    }

    public function edit(Request $req)
    {
        $id = (int) $req->post('id');
        $db = DB::table('guess_word')->where('id', $id);
        $check = $db->first();
        if ($check) {
            $this->validate($req, [
                'word_name' => ['required','string','max:50','regex:/([A-Za-z0-9\-]+)/', 'not_regex:/(\\?|\s)/'],
                'item_image' => 'nullable|mimes:jpeg,jpg,png|max:1000',
                'word_info' => 'required|string|max:190',
                'country' => 'nullable|string|max:190',
                'time' => 'required|integer|min:2'
            ]);
            $image = $check->image;
            if ($req->hasFile('item_image')) {
                $filename = basename($image);
                $path = public_path('uploads');
                if ($filename != null && file_exists($path.'/'.$filename)) {
                    unlink($path.'/'.$filename);
                }
                $filename = Carbon::now()->timestamp.'.'.$req->file('item_image')->getClientOriginalExtension();
                $req->file('item_image')->move($path, $filename);
                $image = env('APP_URL').'/public/uploads/'.$filename;
            }
            $ctry = $req->post('country');
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
            $db->update([
                'image' => $image,
                'info' => $req->post('word_info'),
                'word' => strtoupper($req->post('word_name')),
                'country' => strtolower($ctry),
                'max_time' => $req->post('time')
            ]);
            return back()->with('success', 'Item updated');
        }
    }

    public function del(Request $req)
    {
        $id = (int) $req->post('id');
        $c = DB::table('guess_word')->where('id', $id);
        if ($c->first()) {
            $filename = basename($c->first()->image);
            if ($filename != null) {
                $path = public_path('uploads');
                if ($filename != null && file_exists($path.'/'.$filename)) {
                    unlink($path.'/'.$filename);
                };
            }
            $c->delete();
            return back()->with('success', 'Item deleted');
        } else {
            return back()->with('error', 'Invalid item!');
        }
    }

    public function settUpdate(Request $req)
    {
        $this->validate($req, [
            'reward_amount' => 'required|integer|min:1',
            'retry_chance' => 'required|integer',
            'retry_cost' => 'required|integer',
            'hint_chance' => 'required|integer',
            'hint_cost' => 'required|integer',
            'solve_cost' => 'required|integer',
            'offset' => 'required|integer|between:0,10'
        ]);
        Funcs::setEnv('GW_REWARD', $req->post('reward_amount'), false);
        Funcs::setEnv('GW_RETRY_CHANCE', $req->post('retry_chance'), false);
        Funcs::setEnv('GW_RETRY_COST', $req->post('retry_cost'), false);
        Funcs::setEnv('GW_HINT_CHANCE', $req->post('hint_chance'), false);
        Funcs::setEnv('GW_HINT_COST', $req->post('hint_cost'), false);
        Funcs::setEnv('GW_SOLVE_COST', $req->post('solve_cost'), false);
        Funcs::setEnv('GW_TIME_OFFSET', $req->post('offset'));
        return back()->with('success', 'Settings updated');
    }
}
