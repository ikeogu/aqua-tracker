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
                'pieces' => $this->pieces,
                'price_per_unit' => number_format($this->price_per_unit,2),
                'amount' => number_format($this->amount,2),
                'status' => $this->status,
                'size' => intval($this->size),
            ],

        ];
    }
}
