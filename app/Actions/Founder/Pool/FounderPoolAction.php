<?php

declare(strict_types=1);

namespace App\Actions\Founder\Pool;

use App\Models\PoolPercent;
use App\Models\ReportReferral;
use App\Models\Selling;
use App\Models\SellingPool;
use App\Models\UsdtTransaction;
use App\Models\User;
use Carbon\Carbon;
use Flare;
use Illuminate\Support\Facades\DB;

class FounderPoolAction
{
    public function run(Selling $selling): void
    {
        foreach ([1, 2, 3, 4] as $founder) {
            $this->pool($selling, $founder);
        }
    }

    public function pool(Selling $selling, int $founder)
    {
        $founderName = sprintf('founder%d', $founder);

        $users = User::where('founder_status', '>=', $founder)->get();

        $transactions = $this->getTransactionsSum($founderName);

        $commission = count($users) === 0 ? 0
            : $transactions / count($users);

        DB::beginTransaction();
        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => $founderName,
            'sum' => $transactions,
            'participants' => count($users),
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            $user->wallet += $commission;
            $user->save();

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $commission,
                'type' => $founderName,
                'product' => $selling,
                'data' => [
                    'balance' => $user->wallet,
                    'transactions' => $transactions,
                ]
            ]);
        }

        DB::commit();
    }

    private function getTransactionsSum(string $founderName): float
    {
        $transactionSum = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd');

        $poolPercent = (PoolPercent::where('key', $founderName)->first()->percent / 100);

        return $transactionSum * $poolPercent;
    }
}
