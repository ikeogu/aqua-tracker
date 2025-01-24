<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserDetailsController;
use App\Http\Controllers\Auth\Admin\AdminLoginController;
use App\Http\Controllers\Auth\Admin\RegisterController;
use App\Http\Controllers\Subscription\SubscribedPlanController;
use App\Http\Controllers\Subscription\SubscriptionPlanController;
use App\Http\Middleware\CheckAdminRole;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('guest')
    ->group(function () {

        Route::post('signin', AdminLoginController::class)->name('login');

        Route::get('get-roles',[RegisterController::class, 'getRoles'] );

    });

Route::prefix('admin')->middleware(['auth:sanctum', CheckAdminRole::class])
->group(function () {
    Route::post('invite-admin', RegisterController::class)->name('invite-admin');

    Route::apiResource('subscription-plan', SubscriptionPlanController::class);
    Route::get('fetch-subscribed-plan', [SubscribedPlanController::class, 'index'])->name('subscribed-plan');
    Route::get('dashboard', DashboardController::class)->name('super.admin.dasboard');
    Route::get('user-details/{tenant}', UserDetailsController::class)->name('user-details');
    Route::post('activate-deactive-account/{tenant}', [UserDetailsController::class, 'accountActivation'])->name('user-details');
});