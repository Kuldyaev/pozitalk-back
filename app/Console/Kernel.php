<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('wait:giver')->hourly();
        $schedule->command('usdt-first:cron')->everyTenMinutes();
        $schedule->command('usdt-second:cron')->everyFifteenMinutes();
        $schedule->command('seling:cron')->hourly();
        $schedule->command('pool:cron')->daily();
        $schedule->command('open:access')->everyTenMinutes();
        $schedule->command('deleting_inactive_recipients:cron')->everyTenMinutes();
        $schedule->command('unblock:cron')->everyTenMinutes();
        $schedule->command('arb:cron')->daily();
        $schedule->command('academy-subscribe:cron')->daily();
        $schedule->command('index:cron')->everyMinute();
        $schedule->command('index-auto:cron')->weeklyOn(1, '10:00');
        $schedule->command('index-auto-month:cron')->monthlyOn(1, '10:00');
        $schedule->call(function () {
            Log::info('Scheduler working');
        })->cron('* * * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
