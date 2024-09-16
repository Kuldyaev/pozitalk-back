<?php

declare(strict_types=1);

use App\Http\Controllers\V2\IndexController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'index-token', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [IndexController::class, 'indexInfo']);
    Route::get('/price', [IndexController::class, 'price']);
    Route::get('/statistic', [IndexController::class, 'statistic']);
    Route::get('/history', [IndexController::class, 'history']);
    Route::get('/structure', [IndexController::class, 'structure']);
    Route::get('/check-limit', [IndexController::class, 'checkLimitProgram']);
    Route::post('/buy', [IndexController::class, 'buyVTI']);
    Route::post('/sell', [IndexController::class, 'sellVTI']);
    Route::get('/auto-pay', [IndexController::class, 'autoInfo']);
    Route::put('/auto-pay/{id}', [IndexController::class, 'stopAutoPurchase']);
    Route::post('/auto-pay', [IndexController::class, 'autoPurchase']);
});
