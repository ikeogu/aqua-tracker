<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Beneficiary extends Model
{
    use HasFactory, HasUuids, BelongsToPrimaryModel;

    protected $fillable = [
       'harvest_customer_id',
    ];


    public function getRelationshipToPrimaryModel(): string
    {
        return 'farm';
    }
    public function harvestCustomer()
    {
        return $this->belongsTo(HarvestCustomer::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
