<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class Gateway extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function category(Request $req)
    {
        $data = DB::table('gate_category')->paginate(6);
        return view('admin.gateway_category', compact('data'));
    }

    public function categoryAdd(Request $req)
    {
        $this->validate($req, [
            'name' => 'required|string|max:190',
            'image' => 'required|mimes:jpeg,jpg,png|max:1000',
            'input_desc' => 'required|string',
            'input_type' => 'required|min:1:max:3',
            'country' => 'nullable|max:190'
        ]);
        try {
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
            $path = public_path('uploads');
            $filename = Carbon::now()->timestamp.'.'.$req->file('image')->getClientOriginalExtension();
            $req->file('image')->move($path, $filename);
            DB::table('gate_category')->insert([
                    'name' => $req->post('name'),
                    'image' => env('APP_URL').'/public/uploads/'.$filename,
                    'input_desc' => $req->post('input_desc'),
                    'input_type' => $req->post('input_type'),
                    'country' => $ctry
                ]);
            return back()->with('success', 'Category added successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function categoryEdit(Request $req)
    {
        $db = DB::table('gate_category')->where('id', $req->post('id'));
        $check = $db->first();
        if ($check) {
            $this->validate($req, [
                'name' => 'required|string|max:190',
                'image' => 'nullable|mimes:jpeg,jpg,png|max:1000',
                'input_desc' => 'required|string',
                'input_type' => 'required|min:1:max:3',
                'country' => 'nullable|max:190'
            ]);
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
            $filename = basename($check->image);
            if ($req->hasFile('image')) {
                $path = public_path('uploads');
                if ($filename != null && file_exists($path.'/'.$filename)) {
                    unlink($path.'/'.$filename);
                };
                $filename = Carbon::now()->timestamp.'.'.$req->file('image')->getClientOriginalExtension();
                $req->file('image')->move($path, $filename);
                $db->update(['image' => env('APP_URL').'/public/uploads/'.$filename]);
            }
            $db->update([
                'name' => $req->post('name'),
                'input_desc' => $req->post('input_desc'),
                'input_type' => $req->post('input_type'),
                'country' => $ctry
            ]);
            return back()->with('success', 'Category added successfully');
        } else {
            return back()->with('error', 'Category not found!');
        }
    }

    public function categoryDel(Request $req)
    {
        $id = $req->post('id');
        $db = DB::table('gate_category')->where('id', $id);
        $check = $db->first();
        if ($check) {
            $filename = basename($check->image);
            $path = public_path('uploads');
            if ($filename != null && file_exists($path.'/'.$filename)) {
                unlink($path.'/'.$filename);
            };
            $db->delete();
            DB::table('gateway')->where('category', $id)->delete();
            return back()->with('success', 'Successfully removed selected category and all of its items');
        }
    }
    
    public function view(Request $req)
    {
        $id = $req->get('id');
        $data = [
            'id' => $id,
            'name' => DB::table('gate_category')->where('id', $id)->first()->name,
            'items' => DB::table('gateway')->where('category', $id)->paginate(10)
        ];
        return view('admin.gateway', compact('data'));
    }

    public function add(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|integer',
            'amount' => 'string|required|max:50',
            'points' => 'required|integer',
            'quantity' => 'required|integer'
        ]);
        DB::table('gateway')->insert([
            'category' => $req->post('id'),
            'amount' => $req->post('amount'),
            'points' => $req->post('points'),
            'quantity' => $req->post('quantity')
        ]);
        return back()->with('success', 'Item added successfully!');
    }

    public function del(Request $req)
    {
        DB::table('gateway')->where('id', $req->get('id'))->delete();
        return back()->with('success', 'Item deleted');
    }
}
