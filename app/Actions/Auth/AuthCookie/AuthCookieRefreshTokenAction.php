<?php

declare(strict_types=1);

namespace App\Actions\Auth\AuthCookie;

use App\Actions\Auth\AuthCookie\AuthCookieActionTrait;
use App\Models\User;
use Illuminate\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;

class AuthCookieRefreshTokenAction
{
    use AuthCookieActionTrait;

    final public function run(User $user): CookieJar|Cookie
    {
        return $this->createCookie(
            $user,
            'access_token',
            $this->getUserAbilities($user)
        );
    }
}