<?php

namespace App\Console\Commands;

use App\Models\ArbDeposit;
use App\Models\IaSystem;
use App\Models\IaSystemDeposit;
use App\Models\IaSystemReport;
use App\Models\PoolPercent;
use App\Models\ReportReferral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class IaSystemCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ia-system:cron';

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

            $arbDeposits = IaSystemDeposit::where('is_active', true)
                ->where('is_request', false)
                ->get();

            foreach ($arbDeposits as $arbDeposit) {

                if (Carbon::now()->subMonths($arbDeposit->count_months) >= $arbDeposit->start) {
                    $arbDeposit->is_can_request = true;
                    $arbDeposit->save();
                }

                $user = User::findOrFail($arbDeposit->user_id);

                if ($arbDeposit->start <= Carbon::now()->subWeek()) {

                    $lastCommission = IaSystemReport::where('user_id', $user->id)
                        ->where('type', 'ia_system_com_' . $arbDeposit->id)
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!isset($lastCommission) || $lastCommission->created_at <= Carbon::now()->subDays(6)) {

                        $poolPercent = PoolPercent::where('key', 'ia-system-deposit')
                            ->first();

                        $balance = IaSystem::where('user_id', $user->id)->first();

                        $resulSum = $poolPercent->percent / 100 * $arbDeposit->amount;
                        $balance->balance += $resulSum;

                        $rep = IaSystemReport::create([
                            'user_id' => $user->id,
                            'sum' => $resulSum,
                            'type' => 'ia_system_com_' . $arbDeposit->id,
                        ]);

                        DB::beginTransaction();
                        $rep->save();
                        $balance->save();
                        DB::commit();

                    }
                }
            }
        }
    }
}
