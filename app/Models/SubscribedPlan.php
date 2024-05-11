<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscribedPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'subscription_plan_id',
        'status',
        'reference',
        'amount',
        'start_date',
        'end_date',
        'no_of_months',
        'payment_method',
        'type'


    ];

    public function tenant() : BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    public function subscriptionPlan() : BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
