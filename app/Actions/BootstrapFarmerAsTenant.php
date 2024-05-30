<?php

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Tenant;
use App\Services\PaymentService;
use PhpOffice\PhpSpreadsheet\Calculation\Token\Stack;

class BootstrapFarmerAsTenant
{
    public static function execute(User $user, array $data): Tenant
    {

        $tenant = self::bootstrapTenant($user, $data);

        return $tenant;
    }

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
            'tenant_id' => $tenant->id,
            'team_member_onboarded' => true,

            ])->save();

        $tenant->users()->attach($user->id, [
            'status' => Status::ACTIVE->value,
            'role' => Role::ORGANIZATION_OWNER->value,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        app(PaymentService::class)->addFreePlanToTenant($tenant);


        return $tenant;
    }
}
