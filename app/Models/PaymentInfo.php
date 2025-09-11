<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentInfo extends Model
{
    use HasFactory;

    protected $fillable  = [
        'authorization',
        'auto_renewal',
        'tenant_id'
    ];

     protected $cast = [
        'authorization'  => 'array'
    ];

    public function tenant() : BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
