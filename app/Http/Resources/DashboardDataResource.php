<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardDataResource extends JsonResource
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
            'name' => $this->first_name . ' '. $this->last_name,
            'email' => $this->email,
            "subscription_plan" => 'free',
            'created_at' => $this->created_at,
            'last_seen' => $this?->loginLogs()?->latest()?->first()?->login_at,
            'status' => $this->tenant?->status
        ];
    }
}
