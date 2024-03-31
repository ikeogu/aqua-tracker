<?php

namespace App\Http\Resources;

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
                'profile_photo' => $this->profile_picture,
            ],

            'farms' => $this->farms->map(function ($farm) {
                return [
                    'id' => $farm->id,
                    'name' => $farm->name,
                ];
            }),

            'tenant' => $this->whenLoaded('tenant', function () {
                return [
                    'id' => $this->tenant->id,
                    'type' => 'tenant',
                    'attributes' => [
                        'name' => $this->tenant->name,
                        'organization_name' => $this->tenant->organization_name,
                        'no_of_farms_owned' => $this->tenant->no_of_farms_owned,
                        'capital' => $this->tenant->capital
                    ],
                ];
            }),
        ];
    }
}
