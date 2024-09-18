<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InventoryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }


    public function update(User $user): Response
    {

        if (!$user->hasAnyRole([Role::FARM_ADMIN->value, Role::ORGANIZATION_OWNER->value, Role::SUPER_ADMIN->value])) {
            return Response::denyWithStatus(403, 'You are not authorized to perform this action');
        }

        return Response::allow();
    }
}