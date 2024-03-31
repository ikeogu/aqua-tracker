<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PondResource extends JsonResource
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
            'type' => 'pond',
            'attributes' => [
                'name' => $this->name,
                'location' => $this->location,
                'size' => $this->size,
                'holding_capacity' => $this->holding_capacity,
                'unit' => $this->unit,
                'feed_size' => $this->feed_size,
                'mortality_rate' => $this->mortality_rate,

            ],

            'relationships' => [
                'farm' => [
                    'data' => [
                        'id' => $this->farm->id,
                         'name' => $this->farm->name,
                    ],
                ],
                'batch' => [
                    'data' => [
                        'id' => $this->batch->id,
                        'name' => $this->batch->name,
                    ],
                ],

            ],
        ];
    }
}
