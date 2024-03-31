<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'type' => 'customer',
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'purchases_made' => json_decode($this->data)->purchases_made ?? null,
                'created_at' => $this->created_at,
            ],

            'relationships' => [
                'farm' => [
                    'data' => [
                        'id' => $this->farm_id,
                        'type' => 'farm',
                        'name' => $this->farm->name
                    ]
                ],
                'harvest' => [
                    'data' => [
                        'id' => $this->harvest_id,
                        'type' => 'harvest',
                        'name' => $this->harvest->name
                    ]
                ],

                'purchases' => [
                    'data' => PurchaseResource::collection($this->purchases),
                    'total_size' => $this->purchases->sum('size'),
                    'total_amount' => $this->purchases->sum('amount'),
                    'total_pieces' => $this->purchases->sum('pieces'),
                    'payment_status' => ($this->purchases->where('status', 'paid')->count() == $this->purchases->count() ? 'completed' : 'incomplete'),
                ],

               'purchases_made' => $this->purchases->sum('amount')
            ],
        ];
    }
}
