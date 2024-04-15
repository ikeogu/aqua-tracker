<?php

use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\CustomNotificationController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new \App\Http\Resources\UserResource($request->user());

})->middleware('auth:sanctum');

Route::get('roles', [RolePermissionController::class, 'index']);

Route::get('notifications', CustomNotificationController::class)->middleware('auth:sanctum');
Route::post('notifications/mark-all-as-read', [CustomNotificationController::class, 'markAllAsRead'])->middleware('auth:sanctum');
Route::post('update-profile', SettingController::class)->middleware('auth:sanctum');
