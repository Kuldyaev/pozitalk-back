<?php

declare(strict_types=1);

use App\Http\Controllers\V2\IaSystemController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/ia-system', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/all-deposits', [IaSystemController::class, 'allDeposits']);
    Route::get('/wont-requests', [IaSystemController::class, 'wontRequestDeposits']);
    Route::put('/start', [IaSystemController::class, 'start']);
    Route::put('/close', [IaSystemController::class, 'close']);
});
