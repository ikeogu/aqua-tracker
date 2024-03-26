<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Otp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OtpCode extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'for',
        'otp',
        'expires_at',
    ];


    protected array $cast = [
        'for' => Otp::class,
        'expires_at' => 'datetime',
    ];
}
