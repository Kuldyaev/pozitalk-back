<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsdtWallet;
use App\Models\UsdtTransaction;
use App\Models\User;

class UsdtFirstCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usdt-first:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'usdt';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $wallets = UsdtWallet::
            where('user_id', '>', 0)
            ->where('date', '>', date('Y-m-d H:i:s', strtotime('-6 hours')))
            ->get();
        //проверяем новые транзакции
        if ($wallets) {
            foreach ($wallets as $wallet) {
                $u = User::where('id', $wallet->user_id)->first();
                if ($u) {
                    UsdtWallet::getInfoByAddress($wallet, $u, $wallet->product);
                    sleep(1);
                }

            }
        }
    }

}
