<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HarvestResource extends JsonResource
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
            'type' => 'harvest',
            'attributes' => [
                'name' => $this->name,
                'consultant' => $this->consultant,
                'created_at' => $this->created_at,
            ],

            'relationships' => [
                'farm' => [
                    'data' => [
                        'id' => $this->farm_id,
                        'type' => 'farm'
                    ]
                ],
                'batch' => [
                    'data' => [
                        'id' => $this->batch_id,
                        'type' => 'batch',
                        'name' => $this->batch->name,
                    ]
                ]

            ],
        ];
    }
}
