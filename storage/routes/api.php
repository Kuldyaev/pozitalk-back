<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\V1\AcademyGivingController;
use App\Http\Controllers\V1\Admin\QuestionAnswerController;
use App\Http\Controllers\V1\AdminPanelController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\GiftClubController;
use App\Http\Controllers\V1\GuestController;
use App\Http\Controllers\V1\HierarchyController;
use App\Http\Controllers\V1\LinesController;
use App\Http\Controllers\V1\RoundController;
use App\Http\Controllers\V1\StatisticController;
use App\Http\Controllers\V1\TrainingController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/can-view', [GuestController::class, 'canView']);
Route::get('/serv-time', [RoundController::class, 'servTime']);

Route::group(['prefix' => 'user'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/code', [AuthController::class, 'codeSend']);
    Route::post('/registration', [AuthController::class, 'registration'])->name('registration');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/statistic', [StatisticController::class, 'getStatistic'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/account', [UserController::class, 'getAccount'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/account', [UserController::class, 'addAccount'])->middleware(['jwt.auth', 'is.banned']);
    Route::put('/account', [UserController::class, 'changeAccount'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/settings', [UserController::class, 'settings'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/wallet', [UserController::class, 'walletGet'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/wallet', [UserController::class, 'walletPost'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/show-welcome', [UserController::class, 'showWelcome'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/business', [HierarchyController::class, 'index'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/hierarchy', [UserController::class, 'getHierarchy'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/change-queue', [UserController::class, 'changeActiveQueue'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/manager-accounts', [UserController::class, 'managerAccount'])->middleware(['jwt.auth', 'is.banned']);
});

Route::group(['prefix' => 'round'], function () {
    Route::put('/start', [RoundController::class, 'start'])->middleware(['jwt.auth', 'is.banned']);
    Route::put('/send-gift', [RoundController::class, 'sendGift'])->middleware(['jwt.auth', 'is.banned']);
    Route::put('/confirm', [RoundController::class, 'confirm'])->middleware(['jwt.auth', 'is.banned']);
    Route::put('/cancel-giver', [RoundController::class, 'cancelGiver'])->middleware(['jwt.auth', 'is.banned']);
});

Route::group(['prefix' => 'training'], function () {
    Route::get('/', [TrainingController::class, 'showTraining'])->middleware(['jwt.auth', 'is.banned']);
    Route::put('/', [TrainingController::class, 'update'])->middleware(['jwt.auth', 'is.banned']);
});

Route::group(['prefix' => 'academy-giving'], function () {
    Route::post('/', [AcademyGivingController::class, 'updateOrCreate'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/', [AcademyGivingController::class, 'show'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/all', [AcademyGivingController::class, 'showAll'])->middleware(['jwt.auth', 'is.banned']);
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/users/code', [AdminPanelController::class, 'userCode'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/users', [AdminPanelController::class, 'allUsers'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/users/{id}', [AdminPanelController::class, 'user'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/statistics', [AdminPanelController::class, 'statistic'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/cancel-giver', [AdminPanelController::class, 'cancelGiverAdmin'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::put('/users/change-ref', [AdminPanelController::class, 'changeRef'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::put('/users/blocked', [AdminPanelController::class, 'blocked'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/users/unblock', [AdminPanelController::class, 'unblock'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::apiResource('/question-answer', QuestionAnswerController::class)->middleware(['jwt.auth', 'is.banned', 'admin']);
});

Route::group(['prefix' => 'gift-club'], function () {
    Route::post('/', [GiftClubController::class, 'updateOrCreateClub'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/', [GiftClubController::class, 'showClub'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::delete('/', [GiftClubController::class, 'deleteClub'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/all', [GiftClubController::class, 'showAllClub'])->middleware(['jwt.auth', 'is.banned']);
});

Route::get('/lines', [LinesController::class, 'getLines'])->middleware(['jwt.auth', 'is.banned']);
Route::get('/question-answer', [QuestionAnswerController::class, 'index'])->middleware(['jwt.auth', 'is.banned']);

Route::post('/feedback', [FeedbackController::class, 'store'])->middleware(['jwt.auth', 'is.banned']);
