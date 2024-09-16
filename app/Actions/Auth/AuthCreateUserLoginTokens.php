<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\NewAccessToken;

class AuthCreateUserLoginTokens
{

    /**
     * @return NewAccessToken[]
     */
    public function run(User $user, string $only = null): array
    {
        $accessToken = null;
        $refreshToken = null;

        if (is_null($only) || $only === 'access') {
            $accessToken = $user->createToken(
                'access_token',
                ['login'],
                Config::relativeTokenTimeout('access_token')
            );
        }

        if (is_null($only) || $only === 'refresh') {
            $refreshToken = $user->createToken(
                'refresh_token',
                ['auth:refresh-token'],
                Config::relativeTokenTimeout('refresh_token')
            );
        }

        return [
            'access' => $accessToken,
            'refresh' => $refreshToken
        ];
    }
}