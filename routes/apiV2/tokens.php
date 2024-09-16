<?php

declare(strict_types=1);

use App\Http\Controllers\V2\TokensController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tokens', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [TokensController::class, 'index']);
    Route::get('/history', [TokensController::class, 'history']);
});
