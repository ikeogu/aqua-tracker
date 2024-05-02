<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'type' => 'batch',
            'attributes' => [
                'name' => $this->name,
                'unit_purchase' => $this->unit_purchase,
                'price_per_unit' => intval($this->price_per_unit),
                'amount_spent' => intval($this->amount_spent),
                'fish_specie' => $this->fish_specie,
                'fish_type' => $this->fish_type,
                'vendor' => $this->vendor,
                'status' => $this->status,
                'date_purchased' => $this->date_purchased
            ],
            'farm' => [
                'id' => $this->farm->id,
                'name' => $this->farm->name
            ]
        ];
    }
}
