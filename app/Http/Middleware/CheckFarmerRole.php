<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Role;
use Illuminate\Support\Facades\Auth;

class CheckFarmerRole
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

         if (!$user || !$user->hasAnyRole([
             Role::FARM_ADMIN->value,
             Role::FARM_EMPLOYEE->value,
             Role::FARM_TEAM_OWNER->value,
             Role::VIEW_FARMS->value,
             Role::ORGANIZATION_OWNER->value,
             Role::ORGANIZATION_TEAM_MEMBER->value,

             ])) {
             abort(403, 'Unauthorized.');
         }
        return $next($request);
    }
}
