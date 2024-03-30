<?php

use App\Http\Controllers\Farm\BatchController;
use App\Http\Controllers\Farm\FarmController;
use App\Http\Controllers\Farm\PondController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'farmer-admin', // This middleware group is defined in bootstrap/app.php
])->group(function () {

    Route::group(['prefix' => 'farms'], function () {
        Route::get('/', [FarmController::class, 'index']);
        Route::post('/', [FarmController::class, 'store']);
        Route::get('/{farm}', [FarmController::class, 'show']);
        Route::patch('/{farm}', [FarmController::class, 'update']);
        Route::delete('/{farm}', [FarmController::class, 'destroy']);
    });


    Route::apiResource('{farm}/batch', BatchController::class);
    Route::apiResource('{farm}/pond', PondController::class);
    Route::get('ponds/{farm}/farm-statistics', [PondController::class, 'farmStatictics']);
});
