<?php

namespace App\Models;

use App\Enums\Role;
use Spatie\MediaLibrary\HasMedia;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasUuids;

    protected $table = 'tenants';

    protected $fillable = [
        'organization_name',
        'no_of_farms_owned',
        'capital',
        'data',
        'username',

    ];

     /**
     *
     * RELATIONSHIPS
     *
     */

     public function user(): HasOne
     {
         return $this->hasOne(User::class);
     }

     public function users(): BelongsToMany
     {
         return $this->belongsToMany(User::class, 'tenant_user', 'tenant_id', 'user_id')->withPivot(['status', 'role']);
     }

     public function owner(): User
     {
         return $this->users()->where('role', Role::ORGANIZATION_OWNER)->first();
     }

     public function farms(): HasMany
     {
         return $this->hasMany(Farm::class);
     }


}
