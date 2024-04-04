<?php

namespace App\Models;

use App\Enums\Role;
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
        return $this->belongsToMany(User::class, 'farm_user', 'farm_id', 'user_id')->withPivot(['status','role','data']);
    }

    public function batches() : HasMany
    {
        return $this->hasMany(Batch::class, 'farm_id');
    }

    public function ponds() : HasMany
    {
        return $this->hasMany(Pond::class , 'farm_id');
    }

    public function owner(): User
    {
        return $this->users()->wherePivot('role', Role::FARM_TEAM_OWNER->value)->first();
    }

    public function harvestcustomers(): HasMany
    {
        return $this->hasMany(HarvestCustomer::class, 'farm_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'farm_id');
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(Harvest::class, 'farm_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'farm_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'farm_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'farm_id');
    }
}
