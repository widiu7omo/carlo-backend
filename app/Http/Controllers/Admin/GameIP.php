<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Exception;
use Cache;
use DB;

class GameIP extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function IPCategory(Request $req)
    {
        $data = DB::table('ip_category')->orderBy('id', 'desc')->paginate(6);
        return view('admin.game_ip_category', compact('data'));
    }

    public function updateSettings(Request $req)
    {
        $this->validate($req, [
            'time' => 'required|integer|min:0',
            'round' => 'required|integer|min:0'
        ]);
        \Funcs::setEnv('IP_TIME_OFFSET', $req->post('time'), false);
        \Funcs::setEnv('IP_MAX_ROUND', $req->post('round'));
        return back()->with('success', 'Settings updated');
    }

    public function IPAddCategory(Request $req)
    {
        $this->validate($req, [
            'ip_category_name' => 'required|string|max:79',
            'ip_category_cost' => 'required|integer|min:0',
            'ip_category_reward' => 'required|integer|min:0',
            'ip_category_time' => 'required|integer|min:20',
            'ip_category_row' => 'required|integer|min:3',
            'ip_category_col' => 'required|integer|min:4'
        ]);
        $row = $req->post('ip_category_row');
        $col = $req->post('ip_category_col');
        if (DB::table('ip_category')->where('row', $row)->where('col', $col)->exists()) {
            return back()->with('error', 'Category already exists with same row and column count!');
        }
        DB::table('ip_category')->insert([
            'title' => $req->post('ip_category_name'),
            'cost' => $req->post('ip_category_cost'),
            'reward' => $req->post('ip_category_reward'),
            'time' => $req->post('ip_category_time'),
            'row' => $row,
            'col' => $col
        ]);
        return back()->with('success', 'Category added successfully');
    }
    public function IPEditCategory(Request $req)
    {
        $id = (int) $req->post('id');
        $c = DB::table('ip_category')->where('id', $id);
        $check = $c->first();
        if ($check) {
            $this->validate($req, [
                'update_category_name' => 'required|string|max:79',
                'update_category_cost' => 'required|integer|min:0',
                'update_category_reward' => 'required|integer|min:0',
                'update_category_time' => 'required|integer|min:5',
                'update_category_row' => 'required|integer|min:3',
                'update_category_col' => 'required|integer|min:4'
            ]);
            $row = $req->post('update_category_row');
            $col = $req->post('update_category_col');
            if (DB::table('ip_category')->where('id', '!=', $check->id)->where('row', $row)->where('col', $col)->exists()) {
                return back()->with('error', 'Category already exists with same row and column count!');
            }
            $c->update([
                'title' => $req->post('update_category_name'),
                'cost' => $req->post('update_category_cost'),
                'reward' => $req->post('update_category_reward'),
                'time' => $req->post('update_category_time'),
                'row' => $row,
                'col' => $col
            ]);
            return back()->with('success', 'Category added successfully');
        } else {
            return back()->with('error', "Category not found!");
        }
    }

    public function IPDelCategory(Request $req)
    {
        $id = (int) $req->post('id');
        $c = DB::table('ip_category')->where('id', $id);
        if ($c->first()) {
            $child = DB::table('image_puzzle')->where('category', $id)->get();
            $path = public_path('uploads');
            foreach ($child as $ch) {
                $filename = basename($ch->image);
                if ($filename != null && file_exists($path.'/'.$filename)) {
                    unlink($path.'/'.$filename);
                };
                DB::table('image_puzzle')->where('id', $ch->id)->delete();
            }
            $c->delete();
            return back()->with('success', 'Successfully removed selected category and all of its puzzles');
        }
    }

    
    public function IPView(Request $req)
    {
        $id = $req->get('id');
        $check = DB::table('ip_category')->where('id', $id)->first();
        if (!$check) {
            return back()->with('error', 'Invalid category selected');
        }
        $data = array('id' => $id);
        $data['cat'] = $check->title;
        $data['time'] = $check->time;
        $data['size'] = $check->col.' X '.$check->row;
        $data['q'] = DB::table('image_puzzle')->where('category', $id)->orderBy('id', 'desc')->paginate(11);
        return view('admin.game_ip', compact('data'));
    }
    
    public function IPAdd(Request $req)
    {
        $this->validate($req, ['image' => 'required|mimes:jpeg,jpg,png|max:200']);
        try {
            $path = public_path('uploads');
            $filename = Carbon::now()->timestamp.'.'.$req->file('image')->getClientOriginalExtension();
            $req->file('image')->move($path, $filename);
            DB::table('image_puzzle')->insert([
                'category' => $req->get('id'),
                'image' => env('APP_URL').'/public/uploads/'.$filename
            ]);
            return back()->with('success', 'Image added successfully');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
        return back()->with('success', $cat_id);
    }

    public function IPEdit(Request $req)
    {
        $id = (int) $req->post('id');
        $this->validate($req, ['update_image' => 'required|mimes:jpeg,jpg,png|max:200']);
        $c = DB::table('image_puzzle')->where('id', $id);
        $check = $c->first();
        if ($check) {
            $filename = basename($check->image);
            $path = public_path('uploads');
            if ($filename != null && file_exists($path.'/'.$filename)) {
                unlink($path.'/'.$filename);
            };
            $filename = Carbon::now()->timestamp.'.'.$req->file('update_image')->getClientOriginalExtension();
            $req->file('update_image')->move($path, $filename);
            $c->update(['image' => env('APP_URL').'/public/uploads/'.$filename]);
            return back()->with('success', 'Category added successfully');
        }
    }

    public function IPDel(Request $req)
    {
        try {
            $d = DB::table('image_puzzle')->where('id', $req->post('id'));
            $check = $d->first();
            if ($check) {
                $path = public_path('uploads');
                $filename = basename($check->image);
                if ($filename != null && file_exists($path.'/'.$filename)) {
                    unlink($path.'/'.$filename);
                };
                $d->delete();
            }
            return back()->with('success', 'Puzzle deleted successfully');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
