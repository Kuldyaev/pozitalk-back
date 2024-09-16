<?php

declare(strict_types=1);

use App\Http\Controllers\Academy\AcademyCategoryLatestController;
use App\Http\Controllers\V2\AcademyController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('/exclusive', [AcademyController::class, 'exclusive']);
    Route::get('/relevant', [AcademyController::class, 'relevant']);
    Route::get('/other', [AcademyController::class, 'other']);
    Route::get('/schedule', [AcademyController::class, 'schedule']);
    Route::get('/categories/latest', AcademyCategoryLatestController::class)->name('academy.categories.latest');
    Route::get('/categories/{category}', [AcademyController::class, 'showCategory'])->name('academy.categories.show');
    Route::get('/course', [AcademyController::class, 'showCourse']);
});
