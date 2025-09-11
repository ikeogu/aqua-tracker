<?php

use App\Enums\Role;
use App\Models\LoginLog;
use App\Models\PaymentInfo;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Models\Role as ModelsRole;
use App\Models\SubscribedPlan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Route::webhooks('webhook/paystack', 'paystack');

Route::get('/create-admin', function () {
    $user = User::updateOrCreate([
        'first_name' => 'Super Admin',
        "last_name" => "User",
        'email' => 'aquatrack.services@gmail.com',
   ],
    [
        'password' => Hash::make('@Biology29'),
        'email_verified_at' => now(),
        'fully_onboarded' => true
    ]);

    $role =ModelsRole::where('name',Role::SUPER_ADMIN->value)->first();
    $user->assignRole($role);

});

Route::get('update-logins', function() {
    $logs = LoginLog::whereNull('logout_at')
        ->get();

    foreach ($logs as $log) {
        # code...
        $log->update([
            'logout_at' => $log->updated_at,
            'login_at' => $log->created_at
        ]);
    }

    return "success";
});



Route::get('/subscribe-users', function () {

});
