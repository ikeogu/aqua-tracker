<?php

use App\Enums\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);


Route::get('/subscribe-users', function () {


    SubscriptionPlan::updateOrCreate(
        ['title' => 'Basic Plan'],
        [
            'description' => 'Just basic plan',
            'monthly_price' => 0,
            'duration' => 3,
            "type" => 'free',
            "discount" => 0,
            "limited_to" => [
                'just 90 days'
            ]
        ]
    );

    SubscriptionPlan::updateOrCreate(
        ['title' => 'Premuium Plan'],
        [
            'description' => 'All the feature',
            'monthly_price' => 3500,
            'duration' => 1,
            "type" => 'premium',
            "discount" => 20,
            "limited_to" => [
                'unlimited'
            ]
        ]
    );

    /** @var User $users */
    $users = User::whereRelation('roles', 'name',  Role::ORGANIZATION_OWNER->value)->get();

    foreach ($users as $user) {
        # code...
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        if($tenant){

            app(PaymentService::class)->addFreePlanToTenant($tenant);
        }
    }

    return 'users subscribed';


});
