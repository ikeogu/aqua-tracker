<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'monthly_price',
        'duration',
        "type",
        "discount",
        "limited_to"
    ];

    protected $casts = [
        'limited_to' => 'json',
    ];



    public function applyDiscount(int $no_of_months = 1) : int
    {
        return ($this->discount > 0 ) ? (($this->monthly_price * $no_of_months) *  ($this->discount /100)) : ($this->monthly_price * $no_of_months);
    }
}
