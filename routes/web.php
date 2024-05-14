<?php

use App\Enums\Role;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);


Route::get('/subscribe-users', function () {

    /** @var User $users */
    $users = User::whereRelation('roles', 'name',  Role::ORGANIZATION_OWNER->value)->get();

    foreach ($users as $user) {
        # code...
        app(PaymentService::class)->addFreePlanToTenant($$user->tenant);
    }

    return 'users subscribed';


});
