<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Pemakaian di route: ->middleware('role:superadmin,admin') */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowed = array_map(fn (string $role) => UserRole::from($role), $roles);

        $user = $request->user();

        if (! $user || ! $user->hasRole(...$allowed)) {
            return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
        }

        return $next($request);
    }
}
