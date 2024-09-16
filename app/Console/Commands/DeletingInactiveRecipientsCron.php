<?php

namespace App\Console\Commands;

use App\Models\RoundGiver;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeletingInactiveRecipientsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deleting_inactive_recipients:cron';

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
        $now = Carbon::now()->subHours(24)->toDateTimeString();
        $givers = RoundGiver::where('status_id', 8)->where('start', '<=', $now)->get();

        foreach ($givers as $giver) {
            $request = new \Illuminate\Http\Request();
            $request->replace([
                'user_id' => $giver->round->account->user_id,
                'type' => 2,
                'message' => 'Нарушение правил сообщества.',
            ]);
            (new \App\Http\Controllers\V1\AdminPanelController)->blocked($request);
        }
    }
}
