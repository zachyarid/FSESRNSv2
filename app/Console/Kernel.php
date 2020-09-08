<?php

namespace App\Console;

use App\Jobs\GetFSEFlightLogs;
use App\Jobs\GetFSEPayments;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //Payments ->everyMinute();
        //FlightLog ->everyMinute(); maybe 2
        //GroupAircraft ->every10 minutes
        $schedule->command('job:payments')->cron('*/2 * * * *')->withoutOverlapping(10);
        $schedule->command('job:flightlogs')->cron('*/3 * * * *')->withoutOverlapping(10);
        $schedule->command('job:fbos')->everyFifteenMinutes();
        $schedule->command('job:aircraft')->everyFifteenMinutes();
        $schedule->command('job:aircraftconfig')->monthlyOn(1, '12:00');
        //$schedule->command('job:allins')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
