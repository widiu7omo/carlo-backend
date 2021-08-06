<?php
namespace App\Http\Middleware;

use Closure;

class SecuredAPI
{
    public function handle($request, Closure $next)
    {

        $user = false;
        $token = $request->header('Authorization');
        if ($token == null || $token == '') {
            return redirect()->route('apiloginerror');
        }
        $u = explode('|', $token);
        if (count($u) != 2) {
            return redirect()->route('apiloginerror');
        }
        $user = \App\User::where('id',$u[0])->where('remember_token', $token)->first();
        if ($user) {
			$user->touch();
            $request->merge(['user' => $user]);
            return $next($request);
        }
        return redirect()->route('apiloginerror');
    }
}
