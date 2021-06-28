<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser
{
    public function handle($request, Closure $next)
    {
        if (! Auth::user()->is_active) {
            abort(404, 'Ваш аккаунт заблокирован.');
        }

        return $next($request);
    }
}
