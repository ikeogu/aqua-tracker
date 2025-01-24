<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Farm\BatchController;
use App\Http\Controllers\Farm\BeneficiaryController;
use App\Http\Controllers\Farm\CustomerController;
use App\Http\Controllers\Farm\ExpenseController;
use App\Http\Controllers\Farm\FarmController;
use App\Http\Controllers\Farm\HarvestController;
use App\Http\Controllers\Farm\HarvestCustomerController;
use App\Http\Controllers\Farm\InventoryController;
use App\Http\Controllers\Farm\PondController;
use App\Http\Controllers\Farm\TaskController;
use App\Http\Controllers\FetchAllCustomersController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Subscription\SubscribedPlanController;
use App\Http\Controllers\Subscription\SubscriptionPlanController;
use App\Http\Controllers\TeamMemberInvitation;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Route;

Route::prefix('farmer')->middleware([
    'auth:sanctum',
    'farmer-admin', // This middleware group is defined in bootstrap/app.php
])->group(function () {

    Route::get('{farm}/check-subscription-status', [OrganizationController::class, 'checkSubscription']);
    Route::group(['prefix' => 'farms'], function () {
        Route::get('/', [FarmController::class, 'index']);
        Route::post('/', [FarmController::class, 'store']);
        Route::patch('/{farm}', [FarmController::class, 'update']);
        Route::delete('/{farm}', [FarmController::class, 'destroy']);
    });


    Route::apiResource('{farm}/batch', BatchController::class);
    Route::get('{farm}/fetch-all-batches', [BatchController::class, 'getBatches']);
    Route::apiResource('{farm}/pond', PondController::class);
    Route::get('ponds/{farm}/farm-statistics', [PondController::class, 'farmStatictics']);

    Route::apiResource('{farm}/employee', EmployeeController::class);
    Route::apiResource('{farm}/harvest/{harvest}/customer', HarvestCustomerController::class);
    Route::apiResource('{farm}/inventory', InventoryController::class);
    Route::apiResource('{farm}/harvest', HarvestController::class);
    Route::apiResource('{farm}/expense', ExpenseController::class);

    Route::post('{farm}/harvest/{harvest}/purchase', [PurchaseController::class, 'store']);
    Route::patch('{farm}/harvest/{harvest}/purchase', [PurchaseController::class, 'update']);
    Route::delete('purchase/{purchase}', [PurchaseController::class, 'destroy']);

    Route::get('{farm}/customers', FetchAllCustomersController::class);
    Route::get('{farm}/tasks', [TaskController::class, 'index']);
    Route::post('{farm}/task', [TaskController::class, 'store']);
    Route::patch('{farm}/task/{task}', [TaskController::class, 'update']);
    Route::delete('{farm}/task/{task}', [TaskController::class, 'destroy']);

    Route::get('{farm}/dashboard', DashboardController::class)->name('dashboard');

    Route::post('team-member-invitation', TeamMemberInvitation::class);
    Route::get('list-team-members', [TeamMemberInvitation::class, 'listTeamMembers']);
    Route::patch('update-team-member/{teamMember}', [TeamMemberInvitation::class, 'updateTeamMember']);
    Route::delete('delete-team-member/{teamMember}', [TeamMemberInvitation::class, 'deleteTeamMember']);

    Route::post('delete-all', \App\Http\Controllers\DeleteAllController::class);

    Route::get('{farm}/beneficiaries', [BeneficiaryController::class, 'index']);
    Route::post('{farm}/beneficiary', [BeneficiaryController::class, 'store']);
    Route::delete('{farm}/beneficiary/{beneficiary}', [BeneficiaryController::class, 'destroy']);

    Route::get('billing-history', [SubscribedPlanController::class, 'billingRecords'])->name('billingRecords');
    Route::post('upgrade-plan', [SubscribedPlanController::class, 'upgradePlan'])->name('upgrade');

    Route::post('activate-renewal', [SubscribedPlanController::class, 'activateAutoRenewal'])->name('activateAutoRenewal');
    Route::get('get-premium-plan', [SubscriptionPlanController::class, 'getPremiumPlan']);

    Route::patch('purchase/{purchase}', [PurchaseController::class, 'updatePurchase'])->name('updatePurchase');

});
