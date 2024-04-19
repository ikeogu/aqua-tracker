<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Pond extends Model
{
    use HasFactory, HasUuids, BelongsToPrimaryModel;


    protected $fillable = [
        "name",
        "type",
        "holding_capacity",
        "unit",
        "size",
        "feed_size",
        "mortality_rate",
        "batch_id",
    ];

    protected $cast = [
        'holding_capacity' => 'integer',
        'unit' => 'integer',
        'size' => 'integer',
        'feed_size' => 'integer',
        'mortality_rate' => 'integer',
    ];


    public function getRelationshipToPrimaryModel(): string
    {
        return 'farm';
    }
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function batch() : BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

}
