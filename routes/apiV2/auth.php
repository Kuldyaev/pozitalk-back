<?php

declare(strict_types=1);

use App\Http\Controllers\V2\Auth\AuthController;
use App\Http\Controllers\V2\Auth\AuthLoginController;
use App\Http\Controllers\V2\Auth\AuthLogoutController;
use App\Http\Controllers\V2\Auth\AuthOAuthCallbackProviderController;
use App\Http\Controllers\V2\Auth\AuthOAuthInitProviderController;
use App\Http\Controllers\V2\Auth\AuthRefreshTokenController;
use App\Http\Controllers\V2\Auth\AuthRegistrationController;
use App\Http\Controllers\V2\Auth\AuthResendCodeController;
use App\Http\Controllers\V2\Auth\AuthValidateCodeController;
use App\Http\Controllers\V2\Auth\Email\AuthNewPasswordController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::prefix('{provider}')->group(function () {

        Route::post('/registration', AuthRegistrationController::class);
        Route::post('/login', AuthLoginController::class)->name('auth.login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/validate-code', AuthValidateCodeController::class);
        });

        Route::post('/resend-code', AuthResendCodeController::class);

        Route::post('/oauth-init', AuthOAuthInitProviderController::class);
        Route::post('/oauth-callback', AuthOAuthCallbackProviderController::class)
            ->middleware('auth:sanctum');
    });



    Route::get('/refresh-token', AuthRefreshTokenController::class);

    Route::get('/logout', AuthLogoutController::class);

    Route::delete('drop-by-email', [AuthController::class, 'dropByEmail'])
        ->middleware('feature.hidden');

    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/new-password', AuthNewPasswordController::class)->name('auth.new-password');

        Route::middleware('feature.hidden')->group(function () {
            Route::get('/login-as', [AuthController::class, 'loginAs']);
            Route::delete('/drop-me', [AuthController::class, 'dropMe']);
        });
    });
});
