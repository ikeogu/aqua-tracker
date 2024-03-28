<?php

use App\Exceptions\InvalidOrExpiredOtp;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function(){
            Route::prefix('api/auth')
            ->group(base_path('routes/routes_includes/auth.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/routes_includes/admin.php'));

            Route::middleware('api')
                ->prefix('api/farmer')
                ->group(base_path('routes/routes_includes/farmer.php'));
                

        }

    )
    ->withMiddleware(function (Middleware $middleware) {
        /* $middleware->append(\App\Http\Middleware\UserFullyOnboarded::class);
        $middleware->append(\App\Http\Middleware\InitializeUserPermissionsForCurrentTenant::class); */

        $middleware->append(\Illuminate\Session\Middleware\StartSession::class);
        $middleware->append(\Illuminate\View\Middleware\ShareErrorsFromSession::class);

        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions) {


    })->create();
