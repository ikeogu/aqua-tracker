<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Harvest extends Model
{
    use HasFactory, HasUuids, BelongsToPrimaryModel;


    protected $fillable = [
        'name',
        'consultant',
        'batch_id',

    ];


    public function getRelationshipToPrimaryModel(): string
    {
        return 'farm';
    }

    public function batch() : BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function farm() : BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function customers() : HasMany
    {
        return $this->hasMany(HarvestCustomer::class);
    }

    public function purchases() : HasMany
    {
        return $this->hasMany(Purchase::class);
    }

}
