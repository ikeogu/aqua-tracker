<?php

namespace App\Http\Resources;

use App\Models\SubscribedPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\currentPlan;

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
            "subscription_plan" => $this->tenant->subscribedPlans()->where('status', 'active')->first()->subscriptionPlan->title,
            'created_at' => $this->created_at,
            'last_seen' => $this->loginLogs()->latest()->first()?->login_at,
            'status' => $this->tenant->status,
            'tenant_id' => $this->tenant->id
        ];
    }
}

