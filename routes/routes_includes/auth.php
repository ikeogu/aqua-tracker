<?php

use App\Http\Controllers\Auth\FarmerOnboardingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SigninController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\TeamMemberInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->middleware('guest')
    ->group(function () {
        Route::post('/signup', SignupController::class)->name('register');
        Route::post('/signin', SigninController::class)->name('login');
        Route::post('/forgot-password', ForgotPasswordController::class)->name('password.forgot');
        Route::post('verify-password-reset-otp', [ForgotPasswordController::class, 'verifyPasswordResetOtp']);
        Route::post('auth/email-verification/resend', [VerifyEmailController::class, 'resend'])->name('verification.resend');
    });

Route::prefix('auth')->middleware('auth:sanctum')
    ->group(function () {
        // EMAIL VERIFICATION


        // TWO FACTOR AUTHENTICATION
        Route::post('/reset-password', ResetPasswordController::class)->name('password.reset');

        // LOGOUT
         Route::post('/logout', LogoutController::class)->name('logout');

        //ONBOARDING
        Route::post('/onboarding/farm-owner', FarmerOnboardingController::class)->name('farmer.onboarding')->middleware('verified');
        Route::post('/onboarding/team-member', [TeamMemberInvitation::class, 'onboardTeamMember'])->name('team-member.onboarding');

        Route::get('/user', function () {
            return new \App\Http\Resources\UserResource(Auth::user());
        });
    });

Route::post('auth/email-verification/verify', [VerifyEmailController::class, 'verify'])->name('verification.verify');
