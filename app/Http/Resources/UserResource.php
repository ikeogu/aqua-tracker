<?php

namespace App\Http\Resources;

use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'type' => 'user',
            'attributes' => [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'email_verified_at' => $this->email_verified_at,
                'role' => $this->role,
                'fully_onboarded' => $this->fully_onboarded,
                "team_member_onboarded" => $this->team_member_onboarded,
                'profile_photo' => $this->profile_picture,
                'phone_number' => $this->phone_number,
            ],

            'organizations' => ($this->role === Role::ORGANIZATION_OWNER->value) ? [$this->farmOwner()] : $this->others()
        ];
    }

    public function farmOwner(): array
    {
        return [
            'id' => $this->tenant?->id,
            'type' => 'tenant',
            'attributes' => [

                'organization_name' => $this->tenant?->organization_name,
                'no_of_farms_owned' => $this->tenant?->no_of_farms_owned,
                'capital' => $this->tenant?->capital,
                'location' => $this->tenant?->location,
                'is_subscription_active' => ($this->tenant?->subscribedPlans()->where('status', 'active')->exists()) ? true : false
            ],
            'farms' => $this->tenant?->farms->map(function ($farm) {
                return [
                    'id' => $farm->id,
                    'name' => $farm->name,
                ];
            }),
        ];
    }

    public function others(): mixed
    {

        return $this->tenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'type' => 'tenant',
                'attributes' => [

                    'organization_name' => $tenant->organization_name,
                    'no_of_farms_owned' => $tenant->no_of_farms_owned,
                    'is_subscription_active' => ($tenant?->subscribedPlans()->where('status', 'active')->exists()) ? true : false
                    //'capital' => $tenant->capital
                ],
                'farms' => $tenant->farms->map(function ($farm) {
                    return [
                        'id' => $farm->id,
                        'name' => $farm->name,
                    ];
                }),
            ];
        });
    }
}
