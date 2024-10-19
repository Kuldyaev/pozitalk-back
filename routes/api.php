<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\V1\AuthController;
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


Route::group(['middleware' => 'api','prefix' => 'user'], function () {

     Route::get('/me', [UserController::class, 'me'])->name('user.me');
    



});


Route::get('messages', [MessageController::class, 'index']);
Route::resource('knowledges', KnowledgeController::class);
Route::get('events/categories', [EventCategoryController::class, 'index']);
Route::post('events/categories', [EventCategoryController::class, 'create']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
