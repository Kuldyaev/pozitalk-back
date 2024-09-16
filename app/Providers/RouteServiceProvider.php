<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            Route::prefix('/view')->group(base_path('routes/web.php'));

            Route::middleware('apiV1')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));

            Route::middleware('apiV2')
                ->prefix('api/v2')
                ->name('api.v2.')
                ->group(function () {
                    Route::group(['prefix' => 'test'], base_path('routes/apiV2/test.php'));
                    Route::group([], base_path('routes/apiV2/auth.php'));

                    Route::group([], base_path('routes/apiV2/index-token.php'));
                    Route::group([], base_path('routes/apiV2/arb.php'));
                    Route::group([], base_path('routes/apiV2/buy.php'));
                    Route::group([], base_path('routes/apiV2/tokens.php'));
                    Route::group([], base_path('routes/apiV2/ia-system.php'));

                    Route::middleware(['auth:sanctum', 'ability:auth:login'])->group(function () {
                        Route::group(['prefix' => 'academy'], base_path('routes/apiV2/academy.php'));
                        Route::group([], base_path('routes/apiV2/user.php'));
                    });
                });
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('apiV1', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
