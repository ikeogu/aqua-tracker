<?php

use App\Exceptions\InvalidOrExpiredOtp;
use App\Http\Middleware\CheckFarmerRole;
use App\Http\Middleware\IdentifyAuthUserCurrentTenant;
use App\Http\Middleware\InitializeUserPermissionsForCurrentTenant;
use App\Http\Middleware\UserFullyOnboarded;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
       // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function(){
            Route::prefix('auth')
            ->group(base_path('routes/routes_includes/auth.php'));

            Route::middleware('api')
                ->prefix('admin')
                ->group(base_path('routes/routes_includes/admin.php'));

            Route::middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->prefix('farmer')
                ->group(base_path('routes/routes_includes/farmer.php'));


        }

    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->appendToGroup('farmer-admin', [
            CheckFarmerRole::class,
            UserFullyOnboarded::class,
            InitializeUserPermissionsForCurrentTenant::class,
            IdentifyAuthUserCurrentTenant::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/*',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {


    })->create();