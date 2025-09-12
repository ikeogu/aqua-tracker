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
Route::prefix('farmer')
    ->middleware(['auth:sanctum', 'farmer-admin'])
    ->group(function () {

    Route::get('{farm}/check-subscription-status', [OrganizationController::class, 'checkSubscription']);

    // Routes requiring active subscription
    Route::middleware('active-sub')->group(function () {
        Route::prefix('farms')->group(function () {
            Route::post('/', [FarmController::class, 'store']);
            Route::patch('/{farm}', [FarmController::class, 'update']);
            Route::delete('/{farm}', [FarmController::class, 'destroy']);
        });

        Route::apiResource('{farm}/batch', BatchController::class)->except(['index', 'show']);
        Route::apiResource('{farm}/pond', PondController::class)->except(['index', 'show']);
        Route::apiResource('{farm}/employee', EmployeeController::class)->except(['index', 'show']);
        Route::apiResource('{farm}/harvest/{harvest}/customer', HarvestCustomerController::class)->except(['index', 'show']);
        Route::apiResource('{farm}/inventory', InventoryController::class)->except(['index', 'show']);
        Route::apiResource('{farm}/harvest', HarvestController::class)->except(['index', 'show']);
        Route::apiResource('{farm}/expense', ExpenseController::class)->except(['index', 'show']);

        Route::post('{farm}/harvest/{harvest}/purchase', [PurchaseController::class, 'store']);
        Route::patch('{farm}/harvest/{harvest}/purchase', [PurchaseController::class, 'update']);
        Route::delete('purchase/{purchase}', [PurchaseController::class, 'destroy']);

        Route::post('{farm}/task', [TaskController::class, 'store']);
        Route::patch('{farm}/task/{task}', [TaskController::class, 'update']);
        Route::delete('{farm}/task/{task}', [TaskController::class, 'destroy']);

        Route::patch('update-team-member/{teamMember}', [TeamMemberInvitation::class, 'updateTeamMember']);
        Route::delete('delete-team-member/{teamMember}', [TeamMemberInvitation::class, 'deleteTeamMember']);

        Route::post('team-member-invitation', [TeamMemberInvitation::class, '__invoke']);
        Route::post('delete-all', \App\Http\Controllers\DeleteAllController::class);

        Route::post('{farm}/beneficiary', [BeneficiaryController::class, 'store']);
        Route::delete('{farm}/beneficiary/{beneficiary}', [BeneficiaryController::class, 'destroy']);
        Route::patch('purchase/{purchase}', [PurchaseController::class, 'updatePurchase'])->name('updatePurchase');
    });

    // Read-only routes (index/show)
    Route::get('farms', [FarmController::class, 'index']);

    Route::get('{farm}/fetch-all-batches', [BatchController::class, 'getBatches']);
    Route::get('{farm}/batch/{batch}', [BatchController::class, 'show']);
    Route::get('{farm}/batch', [BatchController::class, 'index']);

    Route::get('ponds/{farm}/farm-statistic', [PondController::class, 'farmStatictics']);
    Route::get('{farm}/pond/{pond}', [PondController::class, 'show']);
    Route::get('{farm}/pond', [PondController::class, 'index']);

    Route::apiResource('{farm}/employee', EmployeeController::class)->only(['index', 'show']);
    Route::apiResource('{farm}/harvest/{harvest}/customer', HarvestCustomerController::class)->only(['index', 'show']);
    Route::apiResource('{farm}/inventory', InventoryController::class)->only(['index', 'show']);
    Route::apiResource('{farm}/harvest', HarvestController::class)->only(['index', 'show']);
    Route::apiResource('{farm}/expense', ExpenseController::class)->only(['index', 'show']);

    Route::get('{farm}/customers', FetchAllCustomersController::class);
    Route::get('{farm}/tasks', [TaskController::class, 'index']);

    Route::get('{farm}/dashboard', DashboardController::class)->name('dashboard');
    Route::get('list-team-members', [TeamMemberInvitation::class, 'listTeamMembers']);
    Route::get('{farm}/beneficiaries', [BeneficiaryController::class, 'index']);

    // Billing & subscription
    Route::get('billing-history', [SubscribedPlanController::class, 'billingRecords'])->name('billingRecords');
    Route::post('upgrade-plan', [SubscribedPlanController::class, 'upgradePlan'])->name('upgrade');
    Route::post('activate-renewal', [SubscribedPlanController::class, 'activateAutoRenewal'])->name('activateAutoRenewal');
    Route::get('get-premium-plan', [SubscriptionPlanController::class, 'getPremiumPlan']);
});
