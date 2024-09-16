<?php

declare(strict_types=1);

namespace App\Actions\Auth\AuthCookie;

use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderEnum;
use App\Models\Auth\AuthProviderStatusEnum;

class AuthCookieFromAuthProviderAction
{
    use AuthCookieActionTrait;

    final public function run(AuthProvider $authProvider, string $action = null): array
    {
        $user = $authProvider->user;
        if ($authProvider->status === AuthProviderStatusEnum::REGISTERED) {
            $user->tokens()->delete();

            return [
                'access' => $this->createCookie($user, 'access_token', $this->getUserAbilities($user), 'access_token'),
                'refresh' => $this->createCookie($user, 'refresh_token', ['auth:refresh-token', 'refresh_token'])
            ];
        }

        if ($authProvider->provider === AuthProviderEnum::EMAIL) {

            if ($action === 'resend_code') {
                return ['access' => $this->createCookie($user, 'access_token', ['auth:validate-code'], 'validate_code')];
            }

            $user->tokens()->delete();
            return [
                'access' => $this->createCookie($user, 'access_token', ['auth:validate-code'], 'validate_code'),
                'refresh' => $this->createCookie($user, 'refresh_token', ['auth:resend-code'], 'resend_code'),
            ];
        } else if ($authProvider->provider === AuthProviderEnum::TELEGRAM) {
            $user->tokens()->delete();

            return [
                'access' => $this->createCookie($user, 'access_token', ['auth:provider'], 'provider'),
            ];
        }

        return [];
    }
}