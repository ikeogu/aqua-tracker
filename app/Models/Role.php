<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * App\Models\Role.
 *
 * @property string $name
 * @property boolean $is_editable
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */

class Role extends SpatieRole
{
    use HasFactory;

   // protected $casts = ['is_editable' => 'boolean'];

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }
}
