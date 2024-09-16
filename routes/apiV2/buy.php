<?php

declare(strict_types=1);

use App\Http\Controllers\V2\ArbController;
use App\Http\Controllers\V2\BuyFromBalanceController;
use App\Http\Controllers\V2\BuyFromTicketsController;
use App\Http\Controllers\V2\BuyFromUsdtController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'buy', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/tickets', [BuyFromTicketsController::class, 'index']);
    Route::post('/balance', [BuyFromBalanceController::class, 'index']);

    Route::get('/usdt', [BuyFromUsdtController::class, 'walletGet']);
    Route::post('/usdt', [BuyFromUsdtController::class, 'walletPost']);
});
