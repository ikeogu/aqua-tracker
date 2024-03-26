<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Farm extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToTenant;


    protected $fillable = [
        'name',
        'location',
        'no_of_pounds',
        'data_established',
    
    ];

    protected $casts = [
        'data_established' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


}
