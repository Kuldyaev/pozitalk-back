<?php

declare(strict_types=1);

namespace App\Actions\Auth\AuthCookie;

use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderEnum;
use App\Models\Auth\AuthProviderStatusEnum;
use App\Models\User;
use Illuminate\Cookie\CookieJar;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Cookie;

trait AuthCookieActionTrait
{
    final public function createCookie(
        User $user,
        string $name,
        array $abilities = [],
        string $timeoutKey = null,
    ): CookieJar|Cookie {
        $timeout = Config::tokenTimeout($timeoutKey ?? $name);

        $token = $user->createToken(
            $name,
            $abilities,
            now()->addMinutes($timeout)
        );

        return cookie(
            $name,
            $token->plainTextToken,
            $timeout,
            sameSite: 'none',
            secure: true,
        );
    }

    final protected function getUserAbilities(User $user): array
    {
        return ['auth:login'];
    }
}