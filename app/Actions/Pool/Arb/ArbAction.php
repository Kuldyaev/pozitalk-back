<?php

declare(strict_types=1);

namespace App\Actions\Pool\Arb;

use App\Models\Arb\ArbTypeEnum;
use App\Models\ArbDeposit;
use App\Models\PoolPercent;
use App\Models\ReportReferral;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArbAction
{
    public function run(): void
    {
        foreach (ArbTypeEnum::cases() as $type) {
            $this->pool($type);
        }
    }

    public function pool(ArbTypeEnum $type): void
    {
        $users = $this->getUsers($type);

        $ids = [1, 8475];
        $users->each(fn($user) => $ids[] = $user->id);

        $users = User::whereIn('id', array_unique($ids))
            ->get();

        $arbDepositSum = ArbDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount');

        $poolPercentSum = PoolPercent::where('key', $type->value)
            ->value('percent');

        $transactionsSum = $arbDepositSum * 0.5 * $poolPercentSum;

        $commission = count($users) === 0 ? 0
            : $transactionsSum / count($users);

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => $type->value,
                    'data' => [
                        'balance' => $user->wallet,
                        'deposit_sum' => $arbDepositSum,
                        'deposit_max_dum' => $type->maxSenlingsSum(),
                    ]
                ]);

                $rep->save();
            }
        }
    }

    protected function getUsers(ArbTypeEnum $type): Collection
    {
        return DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
                WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
                ELSE 0 END) >= ?', [$type->maxSenlingsSum()])
            ->get();
    }
}
