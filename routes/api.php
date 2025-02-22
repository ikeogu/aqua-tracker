<?php

use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\CustomNotificationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Subscription\SubscribedPlanController;
use App\Http\Controllers\Subscription\SubscriptionPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new \App\Http\Resources\UserResource($request->user());

})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])
->group(function () {
    Route::post('update-profile', SettingController::class);
    Route::patch('update-organization/{tenant}', OrganizationController::class);
});

Route::get('roles', [RolePermissionController::class, 'index']);

Route::get('notifications', CustomNotificationController::class)->middleware('auth:sanctum');
Route::post('notifications/mark-all-as-read', [CustomNotificationController::class, 'markAllAsRead'])->middleware('auth:sanctum');
Route::get('fetch-subscription-plan', [SubscriptionPlanController::class, 'index'])->name('subscription.plan.create');
Route::get('verify-payment', [SubscribedPlanController::class, 'verifyPayment'])->name('verifyPayment');


require __DIR__ . '/routes_includes/admin.php';

require __DIR__ . '/routes_includes/auth.php';

require __DIR__ . '/routes_includes/farmer.php';