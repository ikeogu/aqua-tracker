<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at'
    ];


    protected $cast = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];


    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
