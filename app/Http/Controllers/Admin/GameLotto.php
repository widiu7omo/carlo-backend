<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Cache;
use DB;

class GameLotto extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $data = [
            'coins' => strtolower(env('CURRENCY_NAME')).'s',
            'future_winner' => DB::table('lotto_player')
                                    ->where('lotto_player.lotto_won', 1)
                                    ->leftJoin('users', 'lotto_player.userid', '=', 'users.userid')
                                    ->select(
                                        'users.name as name', 
                                        'users.email as email', 
                                        'users.avatar as avatar', 
                                        'users.userid as id'
                                    )
                                    ->get()
        ];
        return view('admin.game_lotto', compact('data'));
    }
	
	public function update(Request $req)
    {
        $this->validate($req, [
            'cost' => 'required|integer|min:0',
            'daily' => 'required|integer|min:0',
            'm_1' => 'required|integer|min:0',
            'm_2' => 'required|integer|min:0',
            'm_3' => 'required|integer|min:0',
            'm_4' => 'required|integer|min:0',
            'm_5' => 'required|integer|min:0'
        ]);
        Funcs::setEnv('GAME_LOTTO_COST', $req->post('cost'), false);
        Funcs::setEnv('GAME_LOTTO_DAILY', $req->post('daily'), false);
        Funcs::setEnv('GAME_LOTTO_MATCH_1', $req->post('m_1'), false);
        Funcs::setEnv('GAME_LOTTO_MATCH_2', $req->post('m_2'), false);
        Funcs::setEnv('GAME_LOTTO_MATCH_3', $req->post('m_3'), false);
        Funcs::setEnv('GAME_LOTTO_MATCH_4', $req->post('m_4'), false);
        Funcs::setEnv('GAME_LOTTO_MATCH_5', $req->post('m_5'));
        return back()->with('success', 'Addedd successfully');
    }

    public function addWinner(Request $req)
    {
        $s = $req->post('s');
        $check1 = DB::table('users')->where('email', $s)->orWhere('userid', $s)->first();
        if ($check1) {
            $check2 = DB::table('lotto_player')->where('userid', $check1->userid);
            if ($check2->first()) {
                $check2->update(['lotto_won' => 1]);
            } else {
                DB::table('lotto_player')->insert(['userid' => $check1->userid, 'lotto_won' => 1]);
            }
            return back()->with('success', 'Addedd successfully');
        } else {
            return back()->withInput()->with('error', 'User not found with this email.');
        }
    }
    
    public function delWinner(Request $req)
    {
        DB::table('lotto_player')->where('userid', $req->get('d'))->update(['lotto_won' => 0]);
        return back()->with('success', 'Successfully deleted');
    }
}
