<?php

declare(strict_types=1);

namespace App\Actions\Auth\AuthTelegram;

use App\Actions\Auth\AuthRegistrationAction;
use App\Models\Auth\AuthProvider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Str;

class AuthOAuthTelegramCreateLinkAction extends AuthRegistrationAction
{
    use AuthOAuthTelegramTrait;

    public function run(?string $referal_invited, $isCastDomain = false): AuthProvider
    {
        $user = $this->newUser([
            'login' => Str::uuid(),
            'referal_invited' => Str::random(),
        ]);

        if ($referal_invited) {
            $user->referal_id = User::where(
                'referal_invited',
                $referal_invited
            )->value('id');
        }

        return DB::transaction(
            fn() => $this->createProvider($user, $isCastDomain)
        );
    }
}