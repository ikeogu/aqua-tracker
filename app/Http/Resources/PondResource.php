<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Pond */
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
            'type' => $this->type,
            'attributes' => [
                'name' => $this->name,
                'location' => $this->location,
                'size' => $this->size,
                'unit_size' => $this->unit_size,
                'holding_capacity' => $this->holding_capacity,
                'unit' => $this->unit,
                'feed_size' =>$this->feed_size,
                'mortality_rate' => $this->mortality_rate,
            ],
            'percentage' => [
                'unit' => round ((($this->unit / $this->holding_capacity) * 100),2),
                'feed_size' => round((($this->feed_size / $this->holding_capacity) * 100),2),
                'mortality_rate' => round((($this->mortality_rate / $this->unit) * 100),2),
                'size' => round((($this->size / $this->holding_capacity) * 100),2),
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
