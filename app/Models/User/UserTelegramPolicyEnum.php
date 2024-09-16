<?php
declare(strict_types=1);

namespace App\Models\User;

enum UserTelegramPolicyEnum: int
{
    case HIDDEN = 1;

    case PUBLIC = 2;

    case FOR_REF = 3;

    public static function slugs(): array
    {
        return [
            self::HIDDEN->value => 'hidden',
            self::PUBLIC ->value => 'public',
            self::FOR_REF->value => 'for-referral',
        ];
    }

    public static function fromSlug(string $value): self
    {
        return self::from(array_flip(self::slugs())[$value]);
    }
}
