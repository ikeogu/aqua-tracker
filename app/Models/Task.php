<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Task extends Model
{
    use HasFactory, HasUuids, BelongsToPrimaryModel;

    protected $fillable = ['title', 'description', 'status', 'start_date','due_date','set_reminder','farm_id'];



    public function getRelationshipToPrimaryModel(): string
    {
        return 'farm';
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }


}
