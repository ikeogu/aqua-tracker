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
                'duration' => $this->duration,
                "type" => $this->type,
                "discount" => $this->discount,
                "best_value" =>   ($this->monthly_price * ($this->discount/100)) * 12,
                "limited_to" => is_string($this->limited_to)  ? json_decode($this->limited_to) : $this->limited_to
            ]
        ];
    }
}
