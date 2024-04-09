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
                'feed_size' =>intval( $this->feed_size),
                'mortality_rate' => intval($this->mortality_rate),

            ],
            'percentage' => [
                'unit' => ($this->unit / $this->holding_capacity) * 100,
                'feed_size' => ($this->feed_size / $this->holding_capacity) * 100,
                'mortality_rate' => ($this->mortality_rate / $this->unit) * 100,
                'size' => ($this->size / $this->holding_capacity) * 100,
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
