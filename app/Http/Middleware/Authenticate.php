<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if ($request->is('api/*')) {
            return route('apiloginerror');
        } else if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
