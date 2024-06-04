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

        $allowedRoles = [
            Role::FARM_ADMIN,
            Role::FARM_EMPLOYEE,
            Role::FARM_TEAM_OWNER,
            Role::VIEW_FARMS,
            Role::EDIT_FARMS,
            Role::ORGANIZATION_OWNER,
            Role::ORGANIZATION_TEAM_MEMBER,
        ];

        if (!$user || !$user->hasAnyRole($allowedRoles)) {
            abort(403, 'Unauthorized access.');
        }
        return $next($request);
    }
}
