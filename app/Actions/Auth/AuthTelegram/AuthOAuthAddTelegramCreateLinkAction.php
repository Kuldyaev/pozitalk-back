<?php

declare(strict_types=1);

namespace App\Actions\Auth\AuthTelegram;

use App\Actions\Auth\AuthRegistrationAction;
use App\Models\Auth\AuthProvider;

use App\Models\Auth\AuthProviderEnum;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Str;

class AuthOAuthAddTelegramCreateLinkAction extends AuthRegistrationAction
{
    use AuthOAuthTelegramTrait;

    public function run(User $user, bool $isCastDomain = false): AuthProvider
    {
        $existsProvider = $user->authProviders->where('provider', AuthProviderEnum::TELEGRAM)->first();

        if ($existsProvider) {
            return $existsProvider;
        }

        return DB::transaction(
            fn() => $this->createProvider($user, $isCastDomain)
        );
    }

}