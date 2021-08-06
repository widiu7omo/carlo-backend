<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use DB;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/index';
    
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($this->guard()->validate($this->credentials($request))) {
            $user = $this->guard()->getLastAttempted();

            if ($user->id != env('ADMIN')) {
                return redirect()
                        ->back()
                        ->withInput($request->only($this->username(), 'remember'))
                        ->withErrors(['email' => 'Use mobile app to access your account!']);
            } elseif ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
            /*
            $check = DB::table('banned_users')
                            ->where('userid', $user->userid)
                            ->orWhere(function ($query) use ($user) {
                                return $query
                                    ->whereNotIn('device_id', [null, 'none'])
                                    ->where('device_id', $user->device_id);
                            })
                            ->first();
            if ($check) {
                $this->incrementLoginAttempts($request);
                return redirect()
                        ->back()
                        ->withInput($request->only($this->username(), 'remember'))
                        ->withErrors(['email' => 'This user has been banned!']);
            } elseif ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
            */
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
}