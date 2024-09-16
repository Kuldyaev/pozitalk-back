<?php
use App\Events\Usdt\UsdtTransactionEvent;
use App\Http\Controllers\V2\TestController;

Route::get('/ws-notify-transaction', [TestController::class, 'testWsNotifyTransaction'])->middleware(['auth:sanctum']);