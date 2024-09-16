<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

final class AuthFindToken
{
    public function run(string $tokenText): ?PersonalAccessToken
    {
        $token = PersonalAccessToken::findToken($tokenText);

        return $token && $token->expires_at > Carbon::now()
            ? $token
            : null;

    }
}