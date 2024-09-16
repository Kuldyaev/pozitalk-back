<?php

declare(strict_types=1);

namespace App\Models\Auth;
use OpenApi\Attributes\Schema;
#[Schema(
    title: 'Статус провайдера авторизации',
    schema: 'AuthProviderStatusEnum',
    type: 'string',
    enum: [self::SENT_CODE, self::EXPIRED_CODE, self::WAIT_PROVIDER, self::REGISTERED],
    example: self::WAIT_PROVIDER,
    description: 'Статус провайдера авторизации'
)]
enum AuthProviderStatusEnum: string
{
    case SENT_CODE = 'sent_code';

    case EXPIRED_CODE = 'expired_code';

    case WAIT_PROVIDER = 'wait_provider';

    case REGISTERED = 'active';

}