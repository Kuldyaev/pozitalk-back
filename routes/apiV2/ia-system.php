<?php

declare(strict_types=1);

use App\Http\Controllers\V2\IaSystemController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'ia-system', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [IaSystemController::class, 'index']);
    Route::get('/history', [IaSystemController::class, 'history']);

    Route::get('/pools', [IaSystemController::class, 'pools']);
    Route::get('/statistic', [IaSystemController::class, 'statistic']);
    Route::get('/calculation-pools', [IaSystemController::class, 'calculationPools']);

    Route::put('/reopen', [IaSystemController::class, 'reopen']);
    Route::put('/change', [IaSystemController::class, 'change']);
});
