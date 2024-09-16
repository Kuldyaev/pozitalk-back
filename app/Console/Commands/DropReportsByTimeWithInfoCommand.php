<?php

namespace App\Console\Commands;

use App\Models\ReportReferral;
use Carbon\Carbon;
use Illuminate\Console\Command;
use \DB;

class DropReportsByTimeWithInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:drop-by-time';

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
        DB::beginTransaction();
        ReportReferral::where('created_at', '=', new Carbon('2024-07-08 01:00:00'))->get()
            ->each(function (ReportReferral $reportReferral) {
                $oldWallet = $reportReferral->member->wallet;
                $sum = $reportReferral->sum;
                $reportReferral->member->update(['wallet' => $oldWallet - $sum]);

                if ($oldWallet > $reportReferral->member->wallet && $reportReferral->delete()) {
                    dump(
                        sprintf(
                            'Deleted: %s(%s): %s-%s=%s',
                            $reportReferral->type,
                            $reportReferral->id,
                            $oldWallet,
                            $sum,
                            $reportReferral->member->wallet
                        )
                    );
                } else {
                    dump(
                        sprintf(
                            'Saved: %s(%s): %s-%s=%s',
                            $reportReferral->type,
                            $reportReferral->id,
                            $oldWallet,
                            $sum,
                            $reportReferral->member->wallet
                        )
                    );
                }
            });

        if (ReportReferral::where('created_at', '=', new Carbon('2024-07-08 01:00:00'))->exists()) {
            dump('Reports not deleted');
            return self::FAILURE;
        }
        DB::commit();
        return Command::SUCCESS;
    }
}
