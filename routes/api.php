<?php

use App\Http\Controllers\V1\AcademyCourseCategoryController;
use App\Http\Controllers\V1\AcademyCourseItemsController;
use App\Http\Controllers\V1\AcademyCourseItemsFileController;
use App\Http\Controllers\V1\AcademyCourseItemsMomentController;
use App\Http\Controllers\V1\AcademyCoursesController;
use App\Http\Controllers\V1\AcademyGivingController;
use App\Http\Controllers\V1\Admin\FeedbackController;
use App\Http\Controllers\V1\Admin\QuestionAnswerController;
use App\Http\Controllers\V1\Admin\RoundController as AdminRoundController;
use App\Http\Controllers\V1\AdminPanelController;
use App\Http\Controllers\V1\ArbBalanceController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\BalanceController;
use App\Http\Controllers\V1\BanerAcademyController;
use App\Http\Controllers\V1\CapitalController;
use App\Http\Controllers\V1\DexnetRequestController;
use App\Http\Controllers\V1\FileController;
use App\Http\Controllers\V1\GiftClubController;
use App\Http\Controllers\V1\GuestController;
use App\Http\Controllers\V1\HierarchyController;
use App\Http\Controllers\V1\IndexController;
use App\Http\Controllers\V1\IndexTokenInfoController;
use App\Http\Controllers\V1\LessonRecordController;
use App\Http\Controllers\V1\LinesController;
use App\Http\Controllers\V1\MoneyWithdrawalController;
use App\Http\Controllers\V1\PayTicketController;
use App\Http\Controllers\V1\PoolController;
use App\Http\Controllers\V1\PoolPercentController;
use App\Http\Controllers\V1\RoundController;
use App\Http\Controllers\V1\ScheduleCourseController;
use App\Http\Controllers\V1\SellingController;
use App\Http\Controllers\V1\StatisticController;
use App\Http\Controllers\V1\TokenRateController;
use App\Http\Controllers\V1\TrainingController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\UserSettingController;
use App\Http\Controllers\V1\VbtPrivateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------
------------------------
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

Route::post('/pay', [PayTicketController::class, 'pay'])->middleware(['jwt.auth', 'is.banned']);

Route::get('/token-rate"', [TokenRateController::class, 'show'])->middleware(['jwt.auth', 'is.banned']);
Route::put('/token-rate"', [TokenRateController::class, 'update'])->middleware(['jwt.auth', 'is.banned']);

Route::group(['prefix' => 'pools'], function () {
    Route::get('/', [PoolController::class, 'pools'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/selling-statistic', [PoolController::class, 'sellingStatistics'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/day-statistic', [PoolController::class, 'dayStatistic'])->middleware(['jwt.auth', 'is.banned']);
});

Route::group(['prefix' => 'user'], function () {
    Route::get('selings', [UserController::class, 'selings'])->middleware(['jwt.auth', 'is.banned']);

    Route::post('crypto-wallet', [UserController::class, 'cryptoWallets'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/active', [UserController::class, 'isActive'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/life-list', [UserController::class, 'lifeList'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/tokenomics', [UserController::class, 'tokenomics'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/email-verified', [AuthController::class, 'emailVerified']);
    Route::post('/code', [AuthController::class, 'codeSend']);
    Route::post('/registration', [AuthController::class, 'registration'])->name('registration');
    Route::post('/password-forgot', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/password-reset', [AuthController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/email-add', [UserSettingController::class, 'addEmail'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/email-change', [UserSettingController::class, 'emailChange'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/email-change-code', [UserSettingController::class, 'emailChangeCode'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/password-change', [UserSettingController::class, 'changePassword'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/phone-add', [UserSettingController::class, 'addPhone'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/phone-verified', [UserSettingController::class, 'verifiedPhone'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/security-question', [UserSettingController::class, 'securityQuestion'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/me', [AuthController::class, 'me'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/referrals-short-info', [UserController::class, 'shortInfoReferrals'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/statistic', [StatisticController::class, 'getStatistic'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/account', [UserController::class, 'getAccount'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/account', [UserController::class, 'addAccount'])->middleware(['jwt.auth', 'is.banned']);
    Route::put('/account', [UserController::class, 'changeAccount'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/settings', [UserController::class, 'settings'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/wallet', [UserController::class, 'walletGet'])->middleware(['jwt.auth', 'is.banned']);
    Route::post('/wallet', [UserController::class, 'walletPost'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/show-welcome', [UserController::class, 'showWelcome'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/business', [HierarchyController::class, 'index'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/next-line', [HierarchyController::class, 'userHierarchy'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/hierarchy', [UserController::class, 'getHierarchy'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/change-queue', [UserController::class, 'changeActiveQueue'])->middleware(['jwt.auth', 'is.banned']);

    Route::get('/manager-accounts', [UserController::class, 'managerAccount'])->middleware(['jwt.auth', 'is.banned']);

    Route::put('/test-result', [UserController::class, 'testResult'])->middleware(['jwt.auth', 'is.banned']);

    Route::post('wallets', [IndexController::class, 'wallets'])->middleware(['jwt.auth', 'is.banned']);

    Route::group(['prefix' => 'capital'], function () {
        Route::group(['prefix' => 'index-token'], function () {
            Route::get('/', [IndexController::class, 'index'])->middleware(['jwt.auth', 'is.banned']);
            Route::get('/want-to-buy', [IndexController::class, 'wantToBuy'])->middleware(['jwt.auth', 'is.banned']);
            Route::post('/buy', [IndexController::class, 'buyVTI'])->middleware(['jwt.auth', 'is.banned']);
            Route::post('/sell', [IndexController::class, 'sellVTI'])->middleware(['jwt.auth', 'is.banned']);
            Route::get('/programs', [IndexController::class, 'programs'])->middleware(['jwt.auth', 'is.banned']);
            Route::get('/check-limit', [IndexController::class, 'checkLimitProgram'])->middleware(['jwt.auth', 'is.banned']);
            Route::post('/auto-pay', [IndexController::class, 'autoPurchase'])->middleware(['jwt.auth', 'is.banned']);
            Route::get('/auto-pay', [IndexController::class, 'getAllAutoPurchases'])->middleware(['jwt.auth', 'is.banned']);
            Route::get('/auto-pay/{id}', [IndexController::class, 'getAutoPurchase'])->middleware(['jwt.auth', 'is.banned']);
            Route::put('/auto-pay/{id}', [IndexController::class, 'stopAutoPurchase'])->middleware(['jwt.auth', 'is.banned']);
        });

        Route::group(['prefix' => 'index-token/info'], function () {
            Route::post('/{id}', [IndexTokenInfoController::class, 'update'])->middleware(['jwt.auth', 'is.banned', 'admin']);
            Route::get('/{id}', [IndexTokenInfoController::class, 'show'])->middleware(['jwt.auth', 'is.banned', 'admin']);
            Route::get('', [IndexTokenInfoController::class, 'index'])->middleware(['jwt.auth', 'is.banned', 'admin']);
        });

        Route::get('/index', [CapitalController::class, 'index'])->middleware(['jwt.auth', 'is.banned']);

        Route::get('/vbt-private', [VbtPrivateController::class, 'index'])->middleware(['jwt.auth', 'is.banned']);

        Route::get('/arb', [ArbBalanceController::class, 'index'])->middleware(['jwt.auth', 'is.banned']);
        Route::put('/arb/reopen', [ArbBalanceController::class, 'reopen'])->middleware(['jwt.auth', 'is.banned']);
        Route::put('/arb/change', [ArbBalanceController::class, 'change'])->middleware(['jwt.auth', 'is.banned']);
        Route::get('/arb/all-deposits', [ArbBalanceController::class, 'allDeposits'])->middleware(['jwt.auth', 'is.banned', 'admin']);
        Route::get('/arb/wont-requests', [ArbBalanceController::class, 'wontRequestDeposits'])->middleware(['jwt.auth', 'is.banned', 'admin']);
        Route::put('/arb/start', [ArbBalanceController::class, 'startArb'])->middleware(['jwt.auth', 'is.banned', 'admin']);
        Route::put('/arb/request', [ArbBalanceController::class, 'requestMoney'])->middleware(['jwt.auth', 'is.banned']);
        Route::put('/arb/close', [ArbBalanceController::class, 'close'])->middleware(['jwt.auth', 'is.banned', 'admin']);
        Route::get('/arb/pools', [ArbBalanceController::class, 'arbPools'])->middleware(['jwt.auth', 'is.banned']);
    });

    Route::group(['prefix' => 'balance'], function () {
        Route::post('/pay', [BalanceController::class, 'pay'])->middleware(['jwt.auth', 'is.banned']);
    });
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
Route::group(['prefix' => 'pool-percents'], function () {
    Route::post('/{poolPercent}', [PoolPercentController::class, 'update'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/', [PoolPercentController::class, 'index'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/{poolPercent}', [PoolPercentController::class, 'show'])->middleware(['jwt.auth', 'is.banned', 'admin']);
});

Route::group(['prefix' => 'academy-giving'], function () {
    Route::post('/', [AcademyGivingController::class, 'updateOrCreate'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/', [AcademyGivingController::class, 'show'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/all', [AcademyGivingController::class, 'showAll'])->middleware(['jwt.auth', 'is.banned']);
});

Route::group(['prefix' => 'dexnet'], function () {
    Route::post('/', [DexnetRequestController::class, 'store'])->middleware(['jwt.auth', 'is.banned']);
    Route::get('/user', [DexnetRequestController::class, 'getUser'])->middleware(['jwt.auth', 'is.banned']);
});

Route::group(['prefix' => 'admin'], function () {
    Route::put('/users/founder-status', [AdminPanelController::class, 'changeFounderStatus'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::put('/selling/selling-restart', [SellingController::class, 'sellingRestart'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::get('admin-charges', [AdminPanelController::class, 'adminCharges'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::group(['prefix' => 'index'], function () {
        Route::get('auto-pays', [IndexController::class, 'adminAutoPay'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    });

    Route::group(['prefix' => 'is-active'], function () {
        Route::get('/', [AdminPanelController::class, 'isActive'])->middleware(['jwt.auth', 'is.banned', 'admin']);
        Route::put('/processed', [AdminPanelController::class, 'isProcessed'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    });

    Route::group(['prefix' => 'dexnet'], function () {
        Route::get('/', [DexnetRequestController::class, 'index'])->middleware(['jwt.auth', 'is.banned', 'admin']);
        Route::put('/approve', [DexnetRequestController::class, 'approve'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    });

    Route::put('/users/gift-tickets', [AdminPanelController::class, 'giftTicket'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::put('/users/gift-usd-com', [AdminPanelController::class, 'giftUsdShareholding'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/users/gift-tokens', [AdminPanelController::class, 'giftTokens'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::post('/gift-giver', [AdminPanelController::class, 'giftAdmin'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/money-withdrawal', [MoneyWithdrawalController::class, 'index'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/money-withdrawal/{id}', [MoneyWithdrawalController::class, 'show'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/money-withdrawal/{id}', [MoneyWithdrawalController::class, 'update'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::get('/rounds/givers', [AdminRoundController::class, 'showGivers'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/reports', [AdminRoundController::class, 'showReports'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/rounds/givers/{id}', [AdminRoundController::class, 'congratulated'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::get('/users/gifts', [AdminPanelController::class, 'allUsersGifts'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/users/priority', [AdminPanelController::class, 'allUsersPriority'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/users/priority', [AdminPanelController::class, 'priorityAccount'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::get('/users/code', [AdminPanelController::class, 'userCode'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/users', [AdminPanelController::class, 'allUsers'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/users/{id}', [AdminPanelController::class, 'user'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::get('/statistics', [AdminPanelController::class, 'statistic'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/cancel-giver', [AdminPanelController::class, 'cancelGiverAdmin'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::put('/users/change-ref', [AdminPanelController::class, 'changeRef'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::put('/users/blocked', [AdminPanelController::class, 'blocked'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/users/unblock', [AdminPanelController::class, 'unblock'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::put('/users/{id}', [AdminPanelController::class, 'userUpdate'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::apiResource('/question-answer', QuestionAnswerController::class)->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::get('/rounds/free-givers', [AdminRoundController::class, 'showFreeGivers'])->middleware(['jwt.auth', 'is.banned', 'admin']);
    Route::post('/rounds/free-givers', [AdminRoundController::class, 'distributionFreeGivers'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::get('/users/accounts/{id}', [AdminPanelController::class, 'account'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::put('/gift-status', [AdminPanelController::class, 'giftStatus'])->middleware(['jwt.auth', 'is.banned', 'admin']);

    Route::get('/ticket-reports', [AdminRoundController::class, 'ticketReports'])->middleware(['jwt.auth', 'is.banned', 'admin']);
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

Route::apiResource('/academy/category', AcademyCourseCategoryController::class)->middleware(['jwt.auth', 'is.banned']);
Route::post('/academy/category/saved', [AcademyCourseCategoryController::class, 'saved'])->middleware(['jwt.auth', 'is.banned']);
Route::apiResource('/academy/course', AcademyCoursesController::class)->middleware(['jwt.auth', 'is.banned']);
Route::apiResource('/academy/item', AcademyCourseItemsController::class)->middleware(['jwt.auth', 'is.banned']);

Route::prefix('/academy/item/{item}')->name('academy.course.item.')
    ->group(function () {
        Route::apiResource('file', AcademyCourseItemsFileController::class);
        Route::apiResource('moment', AcademyCourseItemsMomentController::class);
    })
    ->middleware(['jwt.auth', 'is.banned']);

Route::get('/academy/is-payed', [AcademyCoursesController::class, 'isCoursePayed'])->middleware(['jwt.auth', 'is.banned']);
Route::get('/academy/is-payed-sub', [AcademyCoursesController::class, 'isPayedSub'])->middleware(['jwt.auth', 'is.banned']);

Route::apiResource('/schedule-course', ScheduleCourseController::class)->middleware(['jwt.auth', 'is.banned']);
Route::apiResource('/lesson-record', LessonRecordController::class)->middleware(['jwt.auth', 'is.banned']);

Route::get('/academy', [AcademyCoursesController::class, 'getAcademyMain'])->middleware(['jwt.auth', 'is.banned']);

Route::get('/banner-academy', [BanerAcademyController::class, 'show'])->middleware(['jwt.auth', 'is.banned', 'admin']);
Route::put('/banner-academy', [BanerAcademyController::class, 'update'])->middleware(['jwt.auth', 'is.banned', 'admin']);

Route::post('/upload/file', [FileController::class, 'uploadFile'])->middleware(['jwt.auth', 'is.banned', 'admin']);
Route::get('/upload/file', [FileController::class, 'getFiles'])->middleware(['jwt.auth', 'is.banned', 'admin']);
Route::delete('/upload/file', [FileController::class, 'deleteFile'])->middleware(['jwt.auth', 'is.banned', 'admin']);

Route::post('/money-withdrawal', [MoneyWithdrawalController::class, 'store'])->middleware(['jwt.auth', 'is.banned']);
