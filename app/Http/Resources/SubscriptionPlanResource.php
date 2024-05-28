<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            'type' => 'subscription_plan',
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'monthly_price' => $this->monthly_price,
                'yearly_price' => $this->monthly_price * 12,
                'duration' => $this->duration,
                "type" => $this->type,
                "discount" => $this->discount,
                "best_value" =>  $this->applyDiscount(12) / 100 ,
                "limited_to" => is_string($this->limited_to)  ? json_decode($this->limited_to) : $this->limited_to
            ]
        ];
    }
}
