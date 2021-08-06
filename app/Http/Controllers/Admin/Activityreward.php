<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class Activityreward extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        view()->share('active', DB::table('activity_reward')->where('active', 1)->orderBy('id', 'asc')->get());
        view()->share('inactive', DB::table('activity_reward')->where('active', 0)->orderBy('id', 'asc')->get());
        view()->share('cols', [
            1 => 'bg-green',
            2 => 'bg-teal',
            3 => 'bg-cyan',
            4 => 'bg-blue',
            5 => 'bg-azure',
            6 => 'bg-indigo',
            7 => 'bg-purple',
            8 => 'bg-orange',
            9 => 'bg-pink',
            10 => 'bg-red'
        ]);
        return view('admin.activity_reward');
    }

    public function Edit(Request $req)
    {
        $min = $req->get('min');
        $max = $req->get('max');
        if ($max - $min < 1) {
            return back()->with('error', 'Maximum must be greater than minimum');
        }
        DB::table('activity_reward')->where('id', $req->get('id'))->update([
            'name' => $req->get('name'),
            'min' => $min,
            'max' => $max
        ]);
        return back()->with('success', 'Update was successful');
    }

    public function Toggle(Request $req)
    {
        $id = $req->get('id');
        if ($req->get('a') == 1) {
            $active = 0;
            $reply = 'Selected reward activity disabled';
        } else {
            $active = 1;
            $reply = 'Selected reward activity enabled';
        }
        if ($active == 0) {
            $check = DB::table('activity_reward')->where('id', $id + 1)->first();
            if ($check && $check->active == 1) {
                return back()->with('error', 'You need to disable "'.$check->name.'" first');
            }
        } elseif ($active == 1){
            $check = DB::table('activity_reward')->where('id', $id - 1)->first();
            if ($check && $check->active == 0) {
                return back()->with('error', 'You need to disable "'.$check->name.'" first');
            }
        }
        DB::table('activity_reward')->where('id', $id)->update(['active' =>  $active]);
        return back()->with('success', $reply);
    }
}
