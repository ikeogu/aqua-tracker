<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/** @mixin \App\Models\Farm */
class FarmResource extends JsonResource
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
            'type' => 'farm',
            'attributes' => [
                'name' => $this->name,
                'location' => $this->location,
                'no_of_pounds' => $this->no_of_pounds,
                'data_established' => $this->data_established,
            ],

            'relationships' => [
                'tenant' => [
                    'data' => [
                        'id' => $this->tenant->id,
                        'type' => 'tenant',
                        'attributes' => [
                            'name' => $this->tenant->name,
                            'organization_name' => $this->tenant->organization_name,
                            'no_of_farms_owned' => $this->tenant->no_of_farms_owned,
                            'capital' => $this->tenant->capital
                        ],
                    ],
                ],
                'users' => [
                    'data' => $this->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'type' => 'user',
                        ];
                    }),
                ],
            ],
        ];
    }
}
