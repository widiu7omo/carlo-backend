<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Cache;
use File;
use DB;

class GameScratch extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function catView(Request $req)
    {
        $data = DB::table('scratcher_game')->orderBy('id', 'desc')->paginate(8);
        return view('admin.game_scratch_cat', compact('data'));
    }
    
    public function view(Request $req)
    {
        $data = [
            'id' => 0,
            'name' => '', 
            'cost' => '', 
            'min' => '',
            'max' => '',
            'difficulty' => 3,
            'image' => '',
            'coord' => [13,87,47,79],
            'days' => '',
            'can_purchase' => 1
        ];
        if ($req->has('id')) {
            $db = DB::table('scratcher_game')->where('id', $req->post('id'))->first();
            if ($db) {
                $data = [
                    'id' => $db->id,
                    'name' => $db->name,
                    'cost' => $db->cost,
                    'min' => $db->min,
                    'max' => $db->max,
                    'difficulty' => $db->difficulty,
                    'image' => $db->image,
                    'coord' => explode(',', $db->coord),
                    'days' => $db->days,
                    'can_purchase' => $db->can_purchase
                ];
            }
        }
        return view('admin.game_scratcher', compact('data'));
    }

    public function make(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|integer',
            'name' => 'required|string|max:99',
            'cost' => 'required|integer|min:1',
            'min' => 'required|integer|min:1',
            'max' => 'required|integer|min:100',
            'difficulty' => 'required|integer|between:0,9',
            'days' => 'required|integer|between:1,99999',
            'img_sm' => 'nullable|required_if:id,0|mimes:jpeg,jpg,png|max:300|dimensions:max_width=450,max_height=300',
            'image' => 'nullable|required_if:id,0|mimes:jpeg,jpg,png|max:1000|dimensions:max_width=500,max_height=850',
            'coord_l' => 'required|numeric|between:0,99',
            'coord_r' => 'required|numeric|gt:coord_l|between:1,100',
            'coord_t' => 'required|numeric|between:0,99',
            'coord_b' => 'required|numeric|gt:coord_t|between:1,100'
        ]);
        $coord = implode(",", [
            $req->post('coord_l'),
            $req->post('coord_r'),
            $req->post('coord_t'),
            $req->post('coord_b')
        ]);
        $id = $req->post('id');
        $arr = [
            'name' => $req->post('name'),
            'cost' => $req->post('cost'),
            'min' => $req->post('min'),
            'max' => $req->post('max'),
            'difficulty' => $req->post('difficulty'),
            'coord' => $coord,
            'days' => $req->post('days'),
            'can_purchase' => $req->post('can_purchase') == null ? 1 : 0
        ];
        if ($req->hasFile('image')) {
            $path = public_path('uploads');
            $filename = Carbon::now()->timestamp.'.'.$req->file('image')->getClientOriginalExtension();
            $req->file('image')->move($path, $filename);
            $arr['image'] = env('APP_URL').'/public/uploads/'.$filename;
            $filename2 = (Carbon::now()->timestamp + 1).'.'.$req->file('img_sm')->getClientOriginalExtension();
            $req->file('img_sm')->move($path, $filename2);
            $arr['card'] = env('APP_URL').'/public/uploads/'.$filename2;
        }
        if ($id == 0) {
            DB::table('scratcher_game')->insert($arr);
            return redirect()->route('game_scratch_cat')->with('success', 'Successfully added the card.');
        } else {
            $db = DB::table('scratcher_game')->where('id', $id);
            $check = $db->first();
            if ($check) {
                $oldFile = basename($check->image);
                $oldFile2 = basename($check->card);
                if (isset($arr['image'])) {
                    if ($oldFile != null && file_exists($path.'/'.$oldFile)) {
                        unlink($path.'/'.$oldFile);
                    }
                    if ($oldFile != null && file_exists($path.'/'.$oldFile2)) {
                        unlink($path.'/'.$oldFile2);
                    }
                }
                $db->update($arr);
                return redirect()->route('game_scratch_cat')->with('success', 'Update successful.');
            } else {
                return back()->withInput()->with('error', 'Scratch card not found!');
            }
        }
    }

    public function del(Request $req)
    {
        $id = $req->get('id');
        if ($id == 1) {
            return back()->with('error', 'You cannot delete this card. But still you can update its configuration.');
        }
        $db = DB::table('scratcher_game')->where('id', $id);
        $check = $db->first();
        if ($check) {
            $userGot = DB::table('scratcher_player')
                        ->where('card_id', $check->id)
                        ->where('expiry', '>', Carbon::now())
                        ->orderBy('expiry', 'desc')
                        ->first();
            if ($userGot) {
                return back()->with('error', 'You cannot delete this card at this time. One or more users acquired this card for scratching. If there is no more user get this card from now on then you can delete this card after ' . $userGot->expiry);
            }
            DB::table('scratcher_player')->where('card_id', $check->id)->delete();
            $path = public_path('uploads');
            $oldFile = basename($check->image);
            if ($oldFile != null && file_exists($path.'/'.$oldFile)) {
                unlink($path.'/'.$oldFile);
            }
            $oldFile2 = basename($check->card);
            if ($oldFile2 != null && file_exists($path.'/'.$oldFile2)) {
                unlink($path.'/'.$oldFile2);
            }
            $db->delete();
            return redirect()->route('game_scratch_cat')->with('success', 'Successfully deleted the card.');
        } else {
            return back()->with('error', 'Card not found!');
        }
    }

    public function cleanUp()
    {
        DB::table('scratcher_player')->where('expiry', '<', Carbon::now())->delete();
        return back()->with('success', "Expired cards removed from user's accounts");
    }
}
