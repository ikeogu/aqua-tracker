<?php

use App\Http\Controllers\Auth\FarmerOnboardingController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')
    ->group(function () {
        Route::post('/signup', SignupController::class)->name('register');
       // Route::post('/signin', SignInController::class)->name('login');
       // Route::post('/reset-password', ResetPasswordController::class)->name('password.reset');
       // Route::post('/forgot-password', ForgotPasswordController::class)->name('password.forgot');
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        // EMAIL VERIFICATION
        Route::post('/email-verification/resend', [VerifyEmailController::class, 'resend'])->name('verification.resend');


        // TWO FACTOR AUTHENTICATION

        // LOGOUT
       // Route::post('/logout', LogoutController::class)->name('logout');

        //ONBOARDING
        Route::post('/onboarding/farm-owner', FarmerOnboardingController::class)->name('farmer.onboarding')->middleware('verified');
       // Route::post('/onboarding/team-member', TeamMemberOnboardingController::class)->name('team-member.onboarding');

        Route::get('/user', function () {
            return new \App\Http\Resources\UserResource(auth()->user());
        });
    });

Route::post('/email-verification/verify', [VerifyEmailController::class, 'verify'])->name('verification.verify');
