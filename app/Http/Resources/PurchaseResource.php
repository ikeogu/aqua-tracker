<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'type' => "purchase",
            'attributes' => [
                'pieces' => intval($this->pieces),
                'price_per_unit' => intval($this->price_per_unit),
                'amount' => $this->amount,
                'status' => $this->status,
                'size' => intval($this->size),
            ],

        ];
    }
}
