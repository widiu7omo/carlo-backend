<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Funcs;
use DB;

class RegisterController extends Controller
{
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function apiCreate(Request $req)
    {
        try {
            $ref = $req->json('rb');
            if ($ref != 'none' && strlen($ref) != 13) {
                return ['data' => Funcs::enc(json_encode([
                    'status' => 0,
                    'message' => 'Invalid referral code!'
                ]))];
            }
            $uid = strtoupper(uniqid());
            while (User::where('userid', $uid)->first()) {
                $uid = strtoupper(uniqid());
            };
            $did = $req->json('did');
            if (env('SINGLE_ACCOUNT') == 1) {
                $check = DB::table('banned_users')->orWhere('device_id', $did)->first();
            } else {
                $check = null;
            }
            if ($check) {
                $res = ['status' => 0, 'message' => $check->reason];
            } else {
                $validator = $this->validator($req->all());
                if ($validator->fails()) {
                    return ['data' => Funcs::enc(json_encode([
                        'status' => 0,
                        'message' => $validator->errors()->first()
                    ]))];
                }
                $user = User::create([
                    'userid' => $uid,
                    'email' => $req->json('email'),
                    'name' => $req->json('name'),
                    'password' => bcrypt($req->json('password')),
                    'device_id' => $did,
                    'ip' => \Request::ip(),
                    'country' => $req->json('cc'),
                    'updated_at' => Carbon::now()
                ]);
                $token = $user->id.'|'.Str::random(80);
                DB::table('users')->where('userid', $user->userid)->update(['remember_token' => $token]);
                Funcs::addref($user, $ref);
                //Funcs::updateFCT($user->userid, $user->email, $req->json('fid'));
                $res = ['status' => 1, 'message' => $token, 'u' => $uid];
                return ['data' => Funcs::enc(json_encode($res))];
            }
        } catch (\Exception $e) {
            return ['data' => Funcs::enc(json_encode([
                'status' => 0,
                'message' => 'Could not resister your account with this information!'
            ]))];
        }
    }
}
