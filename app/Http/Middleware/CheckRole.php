<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{

    public function handle($request, Closure $next,...$roles)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if the user has any of the specified permissions
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    return $next($request);
                }
            }
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
