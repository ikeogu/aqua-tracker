<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'type' => 'employee',
            'attributes' => [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'role' => $this->pivot->role,
                'phone_number' => json_decode($this->pivot->data)->phone_number ?? null,
                'status' => $this->pivot->status,
            ],
            'relationships' => [
                'farm' => [
                    'data' => [
                        'id' => $this->pivot->farm_id,
                        'type' => 'farm'
                    ]
                ]
            ],
        ];
    }
}
