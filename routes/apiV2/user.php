<?php

declare(strict_types=1);

use App\Http\Controllers\V2\User\Profile\UserAddOAuthProviderController;
use App\Http\Controllers\V2\User\UserController;
use App\Http\Controllers\V2\User\Profile\UserUpdateProfileInfoController;
use Vi\Controllers\User\Statistic\BalanceHistoryController;
use Vi\Controllers\User\Statistic\UsersLevelController;
use Vi\Controllers\User\Statistic\UserTableListController;

Route::prefix('user')->group(function () {
    Route::get('/me', [UserController::class, 'me'])->name('user.me');
    Route::get('/personal-link', [UserController::class, 'personalLink']);
    Route::patch('/profile-info', UserUpdateProfileInfoController::class)->name('user.profile-info.update');
    Route::post('/profile-info/oauth/{provider}/init', [UserAddOAuthProviderController::class, 'init'])->name('user.profile-info.oauth.init');
    Route::post('/profile-info/oauth/{provider}/callback', [UserAddOAuthProviderController::class, 'callback'])->name('user.profile-info.oauth.callback');
    Route::get('/profile/statuses-and-founders', [UserController::class, 'statusesAndPools']);
});


Route::prefix('user/{user}')->group(function () {
    Route::prefix('statistic')->group(function () {
        Route::get('users-list', UserTableListController::class)
            ->name('user.statistic.users-list');

        Route::get('users-first-level', UsersLevelController::class)
            ->name('user.statistic.users-first-level');

        Route::get('balance-history', BalanceHistoryController::class)
            ->name('user.statistic.balance-history');
    });

});
