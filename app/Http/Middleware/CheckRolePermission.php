<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRolePermission
{

    public function handle($request, Closure $next,...$permissions)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if the user has any of the specified permissions
            foreach ($permissions as $permission) {
                if ($user->hasPermissionTo($permission)) {
                    return $next($request);
                }
            }
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
