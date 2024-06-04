<?php

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Http\Request;

class BootstrapTeamMember
{

    public static function execute(User $user, Request $request): void

    {

        $user->update(
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'team_member_onboarded' => true
            ]
        );

        if ($user->hasAnyRole([Role::EDIT_FARMS->value, Role::FARM_ADMIN->value, Role::VIEW_FARMS->value])) {
            $user->load('tenants');
            $user->tenants->each(function ($tenant) {
                $tenant->pivot->update(['status' => Status::ACTIVE->value]);
            });
        }
    }
}
