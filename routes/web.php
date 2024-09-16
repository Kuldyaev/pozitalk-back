<?php

use App\Events\Test\WebsocketTestEvent;
use App\Events\Usdt\UsdtTransactionEvent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/ws-test', function () {
    broadcast(new WebsocketTestEvent('Test event'));
    broadcast(new UsdtTransactionEvent('Test product', 9597));
    return view('websocket', [
        'http' => false,
        // 'http' => $request->get('http', false),
    ]);
});

Route::get('/test', function () {
    return 200;
});
