<?php

namespace App\Console\Commands;

use App\Models\ArbDeposit;
use App\Models\Seling;
use App\Models\UsdtTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SelingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seling:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $usdtTransactions = UsdtTransaction::where('sum_usd', '>', 1)
            ->where(function ($query) {
                $query->where('product', 'account')
                    ->orWhere('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'token_private')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'arb_deposit')
                    ->orWhere('product', 'index_pay_usdt');
            })
            ->where('address', '!=', 'admin')
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->get();
        foreach ($usdtTransactions as $usdtTransaction) {
            $user = User::where('id', $usdtTransaction->user_id)->first();

            $sum = $usdtTransaction->sum_usd;
            if($usdtTransaction->product == 'arb_deposit') {
                $createdAt = Carbon::parse($usdtTransaction->created_at);

                $start = $createdAt->copy()->subMinutes(30);
                $end = $createdAt->copy()->addMinutes(30);

                $arbDeposits = ArbDeposit::where('user_id', $usdtTransaction->user_id)
                    ->where('amount', $usdtTransaction->sum_usd)
                    ->whereBetween('created_at', [$start, $end])
                    ->where('is_active', true)
                    ->first();

                if(!$arbDeposits)
                    $sum = 0;
            }

            Seling::create([
                'owner_id' => $user->id,
                'member_id' => $user->id,
                'sum' => $sum,
                'product_id' => $usdtTransaction->product,
                'line' => 0,
                'date' => $usdtTransaction->created_at
            ]);

            $i = 1;
            while ($user->referal_id) {
                $referalUser = User::find($user->referal_id);

                Seling::create([
                    'owner_id' => $user->id,
                    'member_id' => $referalUser->id,
                    'sum' => $sum,
                    'product_id' => $usdtTransaction->product,
                    'line' => $i,
                    'date' => $usdtTransaction->created_at
                ]);

                $user = $referalUser;
                $i++;
            }
        }
    }
}
