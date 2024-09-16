<?php

namespace App\Console\Commands;

use App\Actions\Wallets\TicketReportAction;
use App\Models\AcademyCourse;
use App\Models\AcademySubscribe;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class AcademySubscribeCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academy-subscribe:cron';

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
        $activeSubs = AcademySubscribe::where('is_active', true)->where('end_date', '>=', Carbon::now())->get();

        foreach ($activeSubs as $sub) {
            $user = Auth::user();
            $course = AcademyCourse::findOrFail($sub->course_id);

            if ($course->subscription_cost == null && $user->count_avatars < $course->subscription_cost) {
                $sub->is_active = false;
            }
            else {
                $user->count_avatars -= $course->subscription_cost;
                $sub->end_date = Carbon::now()->addDays($course->subscription_days);
                TicketReportAction::create($user->id, $course->subscription_cost, 'accademy_course_sub_' . $course->id);
            }
            $user->save();
            $sub->save();
        }
    }
}
