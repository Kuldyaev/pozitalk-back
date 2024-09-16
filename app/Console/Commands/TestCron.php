<?php

namespace App\Console\Commands;

use App\Actions\Wallets\TokenVestingReportAction;
use App\Models\BanerAcademy;
use App\Models\BannerPayed;
use App\Models\IndexToken;
use App\Models\ReportReferral;
use App\Models\Round;
use App\Models\RoundGiver;
use App\Models\Seling;
use App\Models\TicketReport;
use App\Models\TokenVestingReport;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Models\UserAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cron';

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
        $histories = ReportReferral::where( function ($query) {
            $query->where('type', 'pool-arb-1')
                ->orWhere('type', 'pool-arb-2')
                ->orWhere('type', 'pool-arb-3')
                ->orWhere('type', 'pool-arb-4')
                ->orWhere('type', 'pool-arb-5')
                ->orWhere('type', 'like','arb_com_%');
        })
            ->whereDate('created_at', '2024-08-05')
            ->get();

        foreach ($histories as $history) {
            $new_sum = round($history->sum / 5 * 3, 2);

            $user = User::find($history->member_id);
            $user->wallet = $user->wallet - $history->sum + $new_sum;
            $user->save();

            $history->sum = $new_sum;
            $history->save();
        }
    }
//    public function handle()
//    {
//        $index = IndexToken::where('is_rebalancing', true)->orderBy('id', 'desc')->first();
//
//        $needTime = Carbon::now()->subYear();
//        $time = Carbon::now();
//        $minusIndex = 0.01;
//
//        while ($time > $needTime) {
//            $cc = IndexToken::create([
//                'index' => $index->index - $minusIndex,
//                'bitcoin' => 1,
//                'ethereum' => 1,
//                'arbitrum' => 1,
//                'optimism' => 1,
//                'polygon' => 1,
//                'polkadot' => 1,
//                'ton' => 1,
//                'solana' => 1,
//                'apecoin' => 1,
//                'is_rebalancing' => false,
//                'created_at' => $needTime,
//            ]);
//
//            $needTime = $needTime->addHour();
//            $minusIndex -= 0.000001;
//        }
//    }
//    public function handle()
//    {
//        $users = User::where( function ($query) {
//            $query->where('is_active', true)
//                ->orWhere('commission', '>', 0.3);
//        })
//            ->where('id', '!=', 15)
//            ->get();
//
//        foreach ($users as $user) {
//            $accounts = UserAccount::where('user_id', $user->id)->get();
//
//            foreach ($accounts as $account) {
//                $podarilGivs = RoundGiver::where('account_id', $account->id)->where('status_id', 4)->get();
//
//                if(count($podarilGivs) > 0) {
//
//                    $podaril = 0;
//                    foreach($podarilGivs as $podarilGiv) {
//                        $podaril += $podarilGiv->round->price;
//                    }
//
//                    $rounds = Round::where('account_id', $account->id)->get();
//                    $poluchil = 0;
//                    foreach ($rounds as $round) {
//                        $poluchil += RoundGiver::where('round_id', $round->id)->where('status_id', 4)->count() * $round->price;
//                    }
//
//                    $raznica = $podaril - $poluchil;
//
//                    if($raznica >= 0) {
//                        if($raznica < 50)
//                            $count = 1;
//                        else
//                            $count = intdiv($raznica, 50);
//                    }
//                    else {
//                        $count = 0;
//                    }
//
//                    if($count > 0) {
//                        $minus = TokenVestingReport::where('user_id', $user->id)->where('type', 'gift_base')->sum('count');
//
//                        $count -= $minus;
//
//                        if($count > 0) {
//                            $user->token_vesting += $count * 2500;
//                            $user->save();
//
//                            TokenVestingReportAction::create($user->id, $count * 2500, 'compensation');
//                        }
//                    }
//                }
//            }
//        }
//    }

//    public function handle()
//    {
//        $usdtTransactions = UsdtTransaction::where('sum_usd', '>', 1)
//            ->where(function ($query) {
//                $query->where('product', 'account')
//                    ->orWhere('product', 'bronze')
//                    ->orWhere('product', 'silver')
//                    ->orWhere('product', 'gold')
//                    ->orWhere('product', 'platinum')
//                    ->orWhere('product', 'token_private')
//                    ->orWhere('product', 'dexnet')
//                    ->orWhere('product', 'life_1')
//                    ->orWhere('product', 'life_2')
//                    ->orWhere('product', 'life_3')
//                    ->orWhere('product', 'life_4')
//                    ->orWhere('product', 'life_5')
//                    ->orWhere('product', 'life_6')
//                    ->orWhere('product', 'arb_deposit');
//            })
//            ->where('address', '!=', 'admin')
//            ->get();
//        foreach ($usdtTransactions as $usdtTransaction) {
//            $user = User::where('id', $usdtTransaction->user_id)->first();
//
//            Seling::create([
//                'owner_id' => $user->id,
//                'member_id' => $user->id,
//                'sum' => $usdtTransaction->sum_usd,
//                'product_id' => $usdtTransaction->product,
//                'line' => 0,
//                'date' => $usdtTransaction->created_at
//            ]);
//
//            $i = 1;
//            while ($user->referal_id) {
//                $referalUser = User::find($user->referal_id);
//
//                Seling::create([
//                    'owner_id' => $user->id,
//                    'member_id' => $referalUser->id,
//                    'sum' => $usdtTransaction->sum_usd,
//                    'product_id' => $usdtTransaction->product,
//                    'line' => $i,
//                    'date' => $usdtTransaction->created_at
//                ]);
//
//                $user = $referalUser;
//                $i++;
//            }
//        }
//    }

//    public function test()
//    {
//        $tgs = [
//            346, 190, 7843
//        ];
//
//        $users = User::whereIn('id', $tgs)->get();
//        foreach ($users as $user) {
//            $user->token_vesting += 3000;
//            $user->save();
//
//            TokenVestingReportAction::create($user->id, 3000, 'gift_admin');

//            AcademyPayed::create([
//                'user_id' => $user->id,
//                'academy_course_id' => 4
//            ]);
//        }
//    }
}
