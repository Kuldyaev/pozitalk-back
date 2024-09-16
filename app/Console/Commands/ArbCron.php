<?php

namespace App\Console\Commands;

use App\Models\ArbDeposit;
use App\Models\PoolPercent;
use App\Models\ReportReferral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArbCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arb:cron';

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
        $today = Carbon::today();

        if ($today->isMonday()) {

            $arbDeposits = ArbDeposit::where('is_active', true)
                ->where('is_request', false)
                ->get();

            foreach ($arbDeposits as $arbDeposit) {

                if (Carbon::now()->subMonths($arbDeposit->count_months) >= $arbDeposit->start) {
                    $arbDeposit->is_can_request = true;
                    $arbDeposit->save();
                }

                $user = User::findOrFail($arbDeposit->user_id);

                if ($arbDeposit->start <= Carbon::now()->subWeek()) {

                    $lastCommission = ReportReferral::where('member_id', $user->id)
                        ->where('type', 'arb_com_' . $arbDeposit->id)
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!isset($lastCommission) || $lastCommission->created_at <= Carbon::now()->subDays(6)) {

                        if (in_array($arbDeposit->percent, [5, 6, 7, 8])) {
                            $poolPercent = PoolPercent::where('key', sprintf('arb_com_%s', $arbDeposit->percent))
                                ->first();
                        }

                        if (!isset($poolPercent)) {
                            return false;
                        }

                        $resulSum = $poolPercent->percent / 4 / 100 * $arbDeposit->amount;
                        $user->wallet += $resulSum;

                        $rep = ReportReferral::create([
                            'owner_id' => 1,
                            'member_id' => $user->id,
                            'sum' => $resulSum,
                            'type' => 'arb_com_' . $arbDeposit->id,
                            'comment' => $arbDeposit->id,
                            'product' => $arbDeposit,
                            'data' => [
                                'balance' => $user->wallet,
                                'append_sum' => $resulSum
                            ]
                        ]);

                        DB::beginTransaction();
                        $rep->save();
                        $user->save();
                        DB::commit();

                    }
                }
            }
        }
    }
}
