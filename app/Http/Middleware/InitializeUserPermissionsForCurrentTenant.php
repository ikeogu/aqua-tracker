<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeUserPermissionsForCurrentTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($user = $request->user()) {

            if ($user->tenant) {
                setPermissionsTeamId($user->tenant);

                return $next($request);
            }

           // setPermissionsTeamId(0);
        }

        setPermissionsTeamId(0);

        return $next($request);
    }
}
