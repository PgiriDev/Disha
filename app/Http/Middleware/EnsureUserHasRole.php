<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Allow the request only when the authenticated user has one of the given roles.
     *
     * Usage in routes: ->middleware('role:admin') or ->middleware('role:admin,manager').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole(...$roles)) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
