<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Batch extends Model
{
    use HasFactory, HasUuids, BelongsToPrimaryModel;

    protected $fillable = [
        'name',
        'unit_purchase',
        'price_per_unit',
        'amount_spent',
        'fish_specie',
        'fish_type',
        'vendor',
        'status',
        'date_purchased'
    ];


    protected $casts = [

    ];


    public function getRelationshipToPrimaryModel(): string
    {
        return 'farm';
    }
    public function farm() : BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }



}
