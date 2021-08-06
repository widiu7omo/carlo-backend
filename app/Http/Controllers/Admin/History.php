<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class History extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $e = $req->post('e');
        if (isset($e) && is_numeric($e)) {
            $hist = ['entries' => $e,'data' => DB::table('hist_activities')->orderBy('id', 'desc')->paginate($e)];
        } else {
            $hist = ['entries' => 10,'data' => DB::table('hist_activities')->orderBy('id', 'desc')->paginate(10)];
        }
        return view('admin.history', compact('hist'));
    }

    public function del(Request $req)
    {
        $msg = 'Successfully deleted the entry.';
        $d = DB::table('hist_activities')->where('id', $req->get('id'));
        if ($req->has('deduct')) {
            $hist = $d->first();
            $user = DB::table('users')->where('userid', $hist->userid);
            $check = $user->first();
            if ($hist->points > 0) {
                $user->decrement('balance', $hist->points);
                $msg = 'History removed and amount deducted from user balance.';
            } elseif ($check->balance >= $hist->points && $hist->points < 0) {
                $user->increment('balance', abs($hist->points));
                $msg = 'History removed and amount added to the user balance.';
            } else {
                return back()->with('error', 'Cannot deduct! User balance is less than this requested amount.');
            }
        }
        $d->delete();
        return back()->with('success', $msg);
    }

    public function search(Request $req)
    {
        $toFind = $req->post('s');
        $e = $req->post('e');
        $data = DB::table('hist_activities')
                    ->where('userid', 'like', '%'.$toFind.'%')
                    ->orWhere('network', 'like', '%'.$toFind.'%')
                    ->orWhere('offerid', 'like', '%'.$toFind.'%')
                    ->orWhere('points', 'like', '%'.$toFind.'%')
                    ->orWhere('created_at', 'like', '%'.$toFind.'%')
                    ->paginate($e);
        $hist = ['entries' => $e,'data' => $data];
        return view('admin.history', compact('hist'));
    }
}
