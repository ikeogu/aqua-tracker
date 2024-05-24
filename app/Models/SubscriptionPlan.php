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

        $amount =  ($this->discount > 0 && $no_of_months > 1 ) ? $this->calculateTotalPrice($no_of_months) : ($this->monthly_price * $no_of_months);

        return $this->convertNairaToKobo($amount);
    }

    public function calculateTotalPrice($no_of_months)
    {
        $total_price = $this->monthly_price * $no_of_months;
        $discount_amount = $total_price * ($this->discount / 100);
        $final_price = $total_price - $discount_amount;

        return $final_price;
    }


    function convertNairaToKobo($amountInNaira) {
        return $amountInNaira * 100;
    }
}

