<?php

declare(strict_types=1);

namespace App\Models\Auth;

enum AuthProviderEnum: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case GOOGLE = 'google';
    case TELEGRAM = 'telegram';
}