<?php

use App\Http\Controllers\V2\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/registration', [AuthController::class, 'registration']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/send-code', [AuthController::class, 'codeSend']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware(['jwt.auth', 'is.banned']);
});
