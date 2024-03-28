<?php

use App\Http\Middleware\IdentifyAuthUserCurrentTenant;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'check.user.fully_onboarded',
    IdentifyAuthUserCurrentTenant::class,
])->group(function () {

});
