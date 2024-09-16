<?php

declare(strict_types=1);

use App\Http\Controllers\V2\ArbController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'arb', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ArbController::class, 'index']);
    Route::get('/pools', [ArbController::class, 'arbPools']);
    Route::get('/history', [ArbController::class, 'history']);
    Route::get('/statistic', [ArbController::class, 'statistic']);
    Route::get('/calculation-pools', [ArbController::class, 'calculationPools']);

    Route::put('/reopen', [ArbController::class, 'reopen']);
    Route::put('/change', [ArbController::class, 'change']);
});
