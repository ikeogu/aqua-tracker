<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'type' => 'expense',
            'attributes' => [
                'description' => $this->description,
                'total_amount' => $this->total_amount,
                'splitted_for_batch' => $this->splitted_for_batch,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

        ];
    }
}
