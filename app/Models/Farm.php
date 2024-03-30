<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;


    protected $fillable = [
        'name',
        'location',
        'no_of_ponds',
        'data_established',
        'tenant_id',
    ];

    protected $casts = [
        'data_established' => 'datetime',
    ];

       /**
     *
     * RELATIONSHIPS
     *
     */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'farm_user', 'farm_id', 'user_id')->withPivot(['status','role']);
    }

    public function batches() : HasMany
    {
        return $this->hasMany(Batch::class, 'farm_id');
    }

    public function ponds() : HasMany
    {
        return $this->hasMany(Pond::class , 'farm_id');
    }


}
