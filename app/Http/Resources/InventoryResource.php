<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/** @mixin \App\Models\Inventory */
class InventoryResource extends JsonResource
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
            'type' => 'inventory',
            'attributes' => [
                'name' => $this->name,
                'vendor' => $this->vendor,
                'size' => $this->size,
                'price' => (int) $this->price,
                'quantity' => $this->quantity,
                'amount' => (int) $this->amount,
                'left_over' => $this->left_over,
                'status' => $this->status,
                'created_at' => $this->created_at,
            ],
            'relationships' => [
                'batch' => [
                    'data' => [
                        'id' => $this->batch_id,
                        'type' => 'batch',
                        'attributes' => [
                            'name' => $this->batch->name,
                            'description' => $this->batch->description,
                            'status' => $this->batch->status,
                            'created_at' => $this->batch->created_at,
                        ],
                    ]
                ]
            ],
        ];
    }
}
