<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [

        'pieces',
        'price_per_unit',
        'amount',
        'status',
        'size',
        'farm_id',
        'harvest_id',
        "to_balance",
        "amount_paid",
        'harvest_customer_id',
    ];

    protected $casts = [
        'size' => 'float',
        'pieces' => 'float',
        'price_per_unit' => 'float'
    ];


    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function harvest()
    {
        return $this->belongsTo(Harvest::class);
    }

    public function customer()
    {
        return $this->belongsTo(HarvestCustomer::class);
    }

}