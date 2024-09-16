<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Models\UsdtTransaction;
use App\Models\User;

enum UserStatusEnum: string
{
    case OSNOVATEL_1 = 'osnovatel-1';

    case OSNOVATEL_2 = 'osnovatel-2';

    case OSNOVATEL_3 = 'osnovatel-3';

    case OSNOVATEL_4 = 'osnovatel-4';

    case PARTNER = 'partner';

    case MENTOR = 'mentor';

    case SOVETNIC = 'sovetnic';

    case BASIC = 'base';

    case BRONZE = 'bronze';

    case SILVER = 'silver';

    case GOLD = 'gold';

    case PLATINUM = 'platinum';

    public static function fromUser(User $user): ?self
    {
        if ($user->founder_status) {
            return self::from(sprintf('osnovatel-%d', $user->founder_status));
        }

        // Others statuses (0.3 - basic, 0.5 - boroze, 0.7 - silver, 1 - gold and platinum)
        if ($user->commission) {
            $status = match ($user->commission) {
                0.3 => self::BASIC,
                0.5 => self::BRONZE,
                0.7 => self::SILVER,
                default => null,
            };
            if ($status) {
                return $status;
            }
        }

        $status = UsdtTransaction::where('user_id', $user->id)
            ->whereIn('product', [
                'bronze',
                'silver',
                'gold',
                'platinum',
            ])
            ->orderBy('id', 'desc')
            ->value('product');

        return match ($status) {
            'bronze' => self::BRONZE,
            'silver' => self::SILVER,
            'gold' => self::BRONZE,
            'platinum' => self::SILVER,
            default => null,
        };
    }
}
