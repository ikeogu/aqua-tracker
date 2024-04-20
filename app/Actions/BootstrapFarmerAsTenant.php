<?php

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use App\Models\Tenant;
use PhpOffice\PhpSpreadsheet\Calculation\Token\Stack;

class BootstrapFarmerAsTenant
{
    public static function execute(User $user, array $data): Tenant
    {
       // self::saveUserDetail($user, $data);

        $tenant = self::bootstrapTenant($user, $data);

        return $tenant;
    }

   /*  protected static function saveUserDetail(User $user, array $data): void
    {
        $user->update([
            'first_name' => $data['profile']['first_name'],
            'last_name' => $data['profile']['last_name'],
            'contact' => $data['profile']['contact'],
        ]);

        $user->forceFill(['password' => $data['profile']['password']])->save();
    } */

    protected static function bootstrapTenant(User $user, array $data): Tenant
    {
        /** @var Tenant $tenant */
        $tenant = Tenant::updateOrCreate(
            [
                'username' => $user->email,
                'organization_name' => $data['organization_name']
            ],
            [
                'no_of_farms_owned' => $data['no_of_farms_owned'] ?? null,
                'data' => json_encode($data['team_members'] ?? []),
            ]
        );

        $user->forceFill([
            'fully_onboarded' => true,
            'tenant_id' => $tenant->id

            ])->save();

        $tenant->users()->attach($user->id, [
            'status' => Status::ACTIVE->value,
            'role' => Role::ORGANIZATION_OWNER->value,
            'created_at' => now(),
            'updated_at' => now()
        ]);


        return $tenant;
    }
}
