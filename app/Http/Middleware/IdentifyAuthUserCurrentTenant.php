<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

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
         $user = Auth::user();

         $tenant = $user->tenant ?? $user->tenants()->latest()->first();

         if (!$tenant) { 
             throw new Exception('Unable to identify tenant with payload', 500);
         }

         tenancy()->initialize($tenant);

         return $next($request);
    }
}
