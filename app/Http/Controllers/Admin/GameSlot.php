<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Exception;
use Cache;
use Funcs;
use DB;

class GameSlot extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $db = DB::table('slot_game')->where('active', 1)->get();
        $data = array();
        $data['l'] = array();
        $rows = DB::table('misc')->where('name', 'slot_rows')->first()->data;
        $cols = DB::table('misc')->where('name', 'slot_cols')->first()->data;
        if ($rows == env('SLOT_ROWS') && $cols == env('SLOT_COLS') && count($db) == env('SLOT_WINNING_LINES')) {
            foreach ($db as $d) {
                array_push($data['l'], explode(',', $d->line_array));
            }
        }
        $data['l'] = json_encode($data['l']);
        $data['c'] = DB::table('scratcher_game')->get(['name', 'id']);
        return view('admin.game_slot', compact('data'));
    }

    public function update(Request $req)
    {
        $this->validate($req, [
            'cost' => 'required|integer|min:0',
            'min_match' => 'required|integer|min:3',
            'rows' => 'required|integer|between:3,6',
            'cols' => 'required|integer|between:3,6',
            'speed' => 'required|integer|between:2,5',
            'round' => 'required|integer|min:0',
            'i_val' => 'required|string',
            'linecount' => 'required|integer|between:0,30',
            'icons' => 'required|integer|between:5,10',
            'difficulty' => 'required|integer|between:0,10',
            'free' => 'required|integer|between:0,4',
            'card' => 'required|integer|between:0,4',
            'card_id' => 'required|integer'
        ]);
        $ival = $req->post('i_val');
        $count = $req->post('icons');
        $arr = explode(',', $ival);
        if (count($arr) == $count) {
            foreach ($arr as $a) {
                if (!ctype_digit($a)) {
                    return back()->with('error', 'Enter only values which can be seperated by comma like 1,5,6,10 etc.');
                }
            }
        } else {
            return back()->with('error', 'Total icon values must be '.$count.' which can seperated by comma.');
        }
        Funcs::setEnv('SLOT_COST', $req->post('cost'), false);
        Funcs::setEnv('SLOT_MIN_MATCH', $req->post('min_match'), false);
        Funcs::setEnv('SLOT_ROWS', $req->post('rows'), false);
        Funcs::setEnv('SLOT_COLS', $req->post('cols'), false);
        Funcs::setEnv('SLOT_SPEED', $req->post('speed') * 1000, false);
        Funcs::setEnv('SLOT_DAILY_ROUND', $req->post('round'), false);
        Funcs::setEnv('SLOT_ICON_VAL', $ival, false);
        Funcs::setEnv('SLOT_WINNING_LINES', $req->post('linecount'), false);
        Funcs::setEnv('SLOT_ICONS', $req->post('icons'), false);
        Funcs::setEnv('SLOT_DIFFICULTY', $req->post('difficulty'), false);
        Funcs::setEnv('SLOT_FREE_DIFFICULTY', $req->post('free'), false);
        Funcs::setEnv('SLOT_CARD_DIFFICULTY', $req->post('card'), false);
        Funcs::setEnv('SLOT_CARD_ID', $req->post('card_id'));
        Cache::forget('slot_info');
        return back()->with('success', 'Winning lines updated successfully');
    }

    public function updateLine(Request $req)
    {
        $this->validate($req, ['lines' => 'required|string']);
        $arr = json_decode($req->post('lines'));
        $db = DB::table('slot_game')->orderby('id', 'asc')->get();
        for ($i = 0; $i < max(count($arr), count($db)); $i++) {
            if (isset($arr[$i])) {
                DB::table('slot_game')
                    ->updateOrInsert(
                        ['id' => $i + 1],
                        ['line_array' => implode(",", $arr[$i]), 'active' => 1]
                    );
            } else {
                DB::table('slot_game')->where('id', $db[$i]->id)->update(['active' => 0]);
            }
        }
        DB::table('misc')->where('name', 'slot_rows')->update(['data' => $req->post('rows')]);
        DB::table('misc')->where('name', 'slot_cols')->update(['data' => $req->post('cols')]);
        return back()->with('success', 'Winning lines updated successfully');
    }
}
