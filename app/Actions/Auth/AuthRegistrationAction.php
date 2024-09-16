<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderStatusEnum;
use App\Models\Auth\AuthProviderEnum;
use App\Models\User;
use Carbon\Carbon;

class AuthRegistrationAction
{

    public function newUser(array $attributes = []): User
    {
        return new User($attributes + [
            'role_id' => 1,
            'status_id' => 1,
            'active_queue' => 1,
        ]);
    }

    public function newProviderWithCode(AuthProviderEnum $provider): AuthProvider
    {
        return new AuthProvider([
            'provider' => $provider->value,
            'status' => AuthProviderStatusEnum::SENT_CODE->value,
            'data' => [
                'code' => $this->generateCode(),
                'code_generated_at' => Carbon::now(),
            ]
        ]);
    }

    protected function generateCode(): int
    {
        return rand(100000, 999999);
    }

}