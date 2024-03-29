<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyAuthUserCurrentTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         /** @var User $user */
         $user = auth()->user();

         if (!$user->tenant) { //@phpstan-ignore-line
             throw new Exception('Unable to identify tenant with payload', 500);
         }

         tenancy()->initialize($user->tenant);

         return $next($request);
    }
}
