<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $user = $request->user();

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $next($request);
        }

        abort(403);
    }
}