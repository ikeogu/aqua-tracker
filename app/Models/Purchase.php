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
        'batch_id',
        'size',
        'farm_id',
        'harvest_id',
        'harvest_customer_id',
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
