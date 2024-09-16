<?php

declare(strict_types=1);

namespace App\Models\Arb;

enum ArbTypeEnum: string
{
    case ARB_1 = 'pool-arb-1';

    case ARB_2 = 'pool-arb-2';

    case ARB_3 = 'pool-arb-3';

    case ARB_4 = 'pool-arb-4';

    case ARB_5 = 'pool-arb-5';

    public function maxSenlingsSum(): int
    {
        return match ($this) {
            self::ARB_1 => 5000,
            self::ARB_2 => 15000,
            self::ARB_3 => 25000,
            self::ARB_4 => 50000,
            self::ARB_5 => 100000
        };
    }
}