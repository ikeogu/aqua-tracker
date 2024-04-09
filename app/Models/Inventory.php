<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Inventory extends Model
{
    use HasFactory, HasUuids, BelongsToPrimaryModel;


    protected $fillable = [
        'name',
        'quantity',
        'price',
        'amount',
        'vendor',
        'batch_id',
        'size',
        'left_over',
        'status',
    ];


    public function getRelationshipToPrimaryModel(): string
    {
        return 'farm';
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
