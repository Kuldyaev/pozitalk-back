<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\API\AuthController2;
use App\Http\Controllers\PhoneVerificationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/phone-verification', [PhoneVerificationController::class, 'store']);
Route::post('/phone-code-check', [PhoneVerificationController::class, 'verify']);
Route::group(['middleware' => 'api','prefix' => 'user'], function () {
     Route::get('/me', [UserController::class, 'me'])->name('user.me');
});


Route::prefix('auth')->middleware('api')->controller(AuthController2::class)->group(function(){
    Route::post('login', 'login');
    Route::post('user', 'user');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::get('applications', [ApplicationController::class, 'index']);
Route::apiResource('applications', ApplicationController::class)->only([
            'destroy', 'update', 'store'
        ]);
Route::get('messages', [MessageController::class, 'index']);
Route::resource('knowledges', KnowledgeController::class);
Route::get('events/categories', [EventCategoryController::class, 'index']);
Route::post('events/categories', [EventCategoryController::class, 'create']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});