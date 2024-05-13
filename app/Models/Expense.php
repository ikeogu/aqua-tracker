<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Expense extends Model
{
    use HasFactory, HasUuids, BelongsToPrimaryModel;

    protected $fillable = [

        'description',
        'total_amount',
        'splitted_for_batch',
    ];

    protected $casts = [
        'splitted_for_batch' => 'json',
    ];


    public function getRelationshipToPrimaryModel(): string
    {
        return 'farm';
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
