<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Exception;
use Cache;
use DB;

class GameWheel extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $db = DB::table('wheel')->orderBy('id', 'asc')->get();
        $data = [];
        $data['f'] = [
            'id' => 1, 
            'text' => $db[0]->text, 
            'bg' => $db[0]->bg, 
            'message' => $db[0]->message, 
            'difficulty' => $db[0]->difficulty,
            'card_id' => $db[0]->card_id
        ];
        $o = array();
        for ($i= 1; $i < count($db); $i++) {
            array_push($o, $db[$i]);
        }
        $data["o"] = $o;
        $data["c"] = DB::table('scratcher_game')->get(['name','id']);
        return view('admin.game_wheel', compact('data'));
    }

    public function settings(Request $req)
    {
        $this->validate($req, [
            'wheel_cost' => 'required|integer|min:0',
            'wheel_limit' => 'required|integer|min:0'
        ]);
        \Funcs::setEnv('WHEEL_ROUND_COST', $req->post('wheel_cost'), false);
        \Funcs::setEnv('WHEEL_DAILY_LIMIT', $req->post('wheel_limit'));
        return back()->with('success', 'Successfully updated.');
    }

    public function add(Request $req)
    {
        $this->validate($req, [
            'type' => 'required|integer|between:1,2',
            'coin' => 'nullable|required_if:type,1|integer|between:-999999,999999',
            'coin_color' => 'nullable|required_if:type,1|string|max:7',
            'card' => 'nullable|required_if:type,2|integer',
            'card_color' => 'nullable|required_if:type,2|string|max:7',
            'difficulty' => 'required|integer|between:0,5',
            'message' => 'required|string'
        ]);
        $db = DB::table('wheel')->get(['difficulty']);
        $count = count($db);
        if ($count > 17) {
            return back()->withInput()->with('error', 'You cannot add more than 18 items. There is not enough space to add new item in wheel circle. Try to edit your existing items or delete an item before adding new one.');
        }
        $difficulty = $req->post('difficulty');
        if ($difficulty == 5) {
            if ($db->sum('difficulty') == 5 * $count) {
                return back()->withInput()->with('error', 'All the items cannot be at highest difficulty level');
            }
        }
        if ($req->post('type') == 1) {
            $name = $req->post('coin');
            $color = $req->post('coin_color');
            $id = 0;
        } else {
            $id = $req->post('card');
            $name = DB::table('scratcher_game')->where('id', $id)->first()->name;
            if (strlen($name) > 7) {
                $name = substr($name, 0, 5).'..';
            }
            $color = $req->post('card_color');
        }
        DB::table('wheel')->insert([
            'text' => $name,
            'card_id' => $id,
            'bg' => $color,
            'message' => $req->post('message'),
            'difficulty' => $difficulty
        ]);
        return back()->with('success', 'Item added.');
    }

    public function edit(Request $req)
    {
        $this->validate($req, [
            'type' => 'required|integer|between:1,2',
            'coin' => 'nullable|required_if:type,1|integer|between:-999999,999999',
            'coin_color' => 'nullable|required_if:type,1|string|max:7',
            'card' => 'nullable|required_if:type,2|integer',
            'card_color' => 'nullable|required_if:type,2|string|max:7',
            'difficulty' => 'required|integer|between:0,5',
            'message' => 'required|string'
        ]);
        $difficulty = $req->post('difficulty');
        $wheel = DB::table('wheel')->where('id', $req->post('id'));
        if ($difficulty == 5) {
            $db = DB::table('wheel')->get(['difficulty']);
            if ($db->sum('difficulty') - $wheel->first()->difficulty + 5 == 5 * count($db)) {
                return back()->with('error', 'All the items cannot be at highest difficulty level');
            }
        }
        if ($req->post('type') == 1) {
            $name = $req->post('coin');
            $color = $req->post('coin_color');
            $id = 0;
        } else {
            $id = $req->post('card');
            $name = 'CARD';
            /*
            $name = DB::table('scratcher_game')->where('id', $id)->first()->name;
            if (strlen($name) > 7) {
                $name = substr($name, 0, 5).'..';
            }
            */
            $color = $req->post('card_color');
        }
        $wheel->update([
            'text' => $name,
            'card_id' => $id,
            'bg' => $color,
            'message' => $req->post('message'),
            'difficulty' => $difficulty
        ]);
        return back()->with('success', 'Item updated.');
    }

    public function del(Request $req)
    {
        $id = $req->post('id');
        if ($id == 1) {
            return back()->with('error', 'This item cannot be deleted.');
        }
        DB::table('wheel')->where('id', $id)->delete();
        return back()->with('success', 'Item deleted.');
    }

    public function replace(Request $req)
    {
        $db = DB::Table('wheel')->where('id', 1);
        $free = env('WHEEL_FREE_CHANCE') == 1;
        $db->update(['text' => $free ? '0' : 'FREE']);
        \Funcs::setEnv('WHEEL_FREE_CHANCE', $free ? 0 : 1);
        return back()->with('success', 'First item method switched.');
    }
}
