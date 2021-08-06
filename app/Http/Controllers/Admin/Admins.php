<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Hash;

class Admins extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        $data = DB::table('users')->where('id', env('ADMIN'))->first();
        return view('admin.admins', compact('data'));
    }

    public function update(Request $req)
    {
        $this->validate($req, [
            'name' => 'required|string|between:3,190',
            'email' => 'required|string|email|max:190',
            'password' => 'required|string|between:8,40',
            'password_1' => 'nullable|string|between:8,40',
            'password_2' => 'nullable|string|between:8,40'
        ]);
        $db = DB::table('users')->where('id', env('ADMIN'));
        $adm = $db->first();
        $pass = $req->post('password_1');
        $changePass = false;
        if (Hash::check($req->post('password'), $adm->password)) {
            if ($pass != '' && $pass != null) {
                if ($pass == $req->post('password_2')) {
                    $changePass = true;
                } else {
                    return back()->withInput()->with('error', 'New password and confirmation password did not match!');
                }
            }
            $email = $req->post('email');
            if ($email != $adm->email) {
                $check = DB::table('users')->where('email', $email)->first();
                if ($check) {
                    return back()->withInput()->with('error', 'This email address already exist in database. Provide your unique email address.');
                }
            }
            if ($changePass) {
                $db->update([
                    'name' => $req->post('name'),
                    'email' => $email,
                    'password' => bcrypt($pass)
                ]);
            } else {
                $db->update([
                    'name' => $req->post('name'),
                    'email' => $email
                ]);
            }
            return back()->with('success', 'Admin profile updated');
        } else {
            return back()->withInput()->with('error', 'Provide your current password!');
        }
    }

    public function change(Request $req)
    {
        $this->validate($req, [
            'a_email' => 'required|string|email|max:190',
            'password' => 'required|string|between:8,40'
        ]);
        $admin = DB::table('users')->where('id', env('ADMIN'))->first();
        if (Hash::check($req->post('password'), $admin->password)) {
            $check = DB::table('users')->where('email', $req->post('a_email'))->first();
            if ($check) {
                if ($check->id == env('ADMIN')) {
                    return back()->withInput()->with('error', 'You are already the Admin therefore no change has been made.');
                } else {
                    \Funcs::setEnv('ADMIN', $check->id);
                    \Auth::logout();
                    \Session::flush();
                    return redirect(env('APP_URL').'/login');
                }
            } else {
                return back()->withInput()->with('error', 'User does not exist with this email address. First register an account with this email address.');
            }
        } else {
            return back()->withInput()->with('error', 'Enter your valid password!');
        }
    }

    public function saveNote(Request $req)
    {
        DB::table('misc')->updateOrInsert(['name' => 'admin_note'], ['data' => $req->post('a_note')]);
        return back()->with('success', 'Admin note saved.');
    }
}
