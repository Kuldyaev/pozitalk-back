<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class MacrosServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Config::macro('tokenTimeout', function (string $tokenName): int {
            return Config::get(sprintf('auth.timeout.%s', $tokenName));
        });
        Config::macro('relativeTokenTimeout', function (string $tokenName): Carbon {
            return Carbon::now()->addMinutes(Config::tokenTimeout($tokenName));
        });
    }
}
