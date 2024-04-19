<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class HarvestCustomer extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'harvest_customers';

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'purchases_made',
        'data',
        'farm_id',
        'harvest_id'
    ];



    public function farm() : BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function harvest() : BelongsTo
    {
        return $this->belongsTo(Harvest::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    

}
