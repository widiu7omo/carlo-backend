<?php
namespace App\Http\Middleware;

use Auth;
use Closure;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        $auths = Auth::user();
        if ($auths->id == env('ADMIN')) {
            $n = \DB::table('misc')->where('name','admin_note')->first();
            \View::share('admin_share', [
                'note' => $n ? $n->data : '',
                'name' => $auths->name,
                'avatar' => $auths->avatar
            ]);
            return $next($request);
        }
        return redirect()->route('home');
    }
}