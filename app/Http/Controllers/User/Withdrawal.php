<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
use DB;

class Withdrawal extends Controller
{
    public function get(Request $req)
    {
        $user = $req['user'];
        $cc = $req->get('cc');
        $cat = DB::table('gate_category')->where('country', 'all')->orWhere('country', 'LIKE', '%'.$cc.'%')->get();
        $res = array();
        foreach ($cat as $c) {
            $items = DB::table('gateway')->where('category', $c->id)->get(['id','quantity','amount','points']);
            array_push($res, [
                'name' => $c->name,
                'image' => $c->image,
                'input_desc' => $c->input_desc,
                'input_type' => $c->input_type,
                'items' => $items
            ]);
        }
        $hist = DB::table('gate_request')->where('userid', $user->userid)->orderBy('id', 'desc')->get(['g_name','is_completed','message']);
        return ['status' => 1, 'balance' => $user->balance, 'cat' => $res, 'hist' => $hist];
    }

    public function post(Request $req)
    {
        $user = $req['user'];
        $id = $req->json('wid');
        $acc = $req->json('acc');
        $cc = $req->json('cc');
        $db = DB::table('gateway')->where('id', $id);
        $check = $db->first();
        if (!$check) {
            return ['status' => 0, 'message' => 'Gift item not found!'];
        }
        if ($check->points > $user->balance) {
            return ['status' => 0, 'message' => 'Insufficient balance'];
        }
        if ($check->quantity < 1) {
            return ['status' => 0, 'message' => 'Not available in stock, come back later.'];
        }
        $cat = DB::table('gate_category')->where('id', $check->category)->first();
        if (!$cat) {
            return ['status' => 0, 'message' => 'Gift category not found!'];
        }
        $user->decrement('balance', $check->points);
        $user->increment('pending', $check->points);
        $db->decrement('quantity', 1);
        DB::table('gate_request')->insert([
            'userid' => $user->userid,
            'g_name' => $cat->name . ' ' . $check->amount,
            'points' => $check->points,
            'to_acc' => $acc,
			'country' => $cc,
            'message' => '',
            'created_at' => Carbon::now()
        ]);
        return ['status' => 1, 'message' => $check->points];
    }
}
