<?php

namespace App\Console;

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

        // run job specifically for this app
        $search = storage_path() . '/logs/queue.log';
        $cmd1   = 'ps -xf | grep \'[q]ueue:work\' | grep \'' . $search .'\'';
        $rst    = exec($cmd1);
        if (stripos($rst, $search) === false) {
            // \Log::info($search . ' started...');

            // --tries=1 - default all job to only run one time
            // --sleep=2 - seconds in case we hit API limit
            // --timeout=300 - seconds or max of 5 minutes
            // these are suggested defaults and can be
            // override on the queue object itself
            // - see laravel doc on this topic
            $cmd2 = 'queue:work --queue=default --tries=1 --sleep=2 --timeout=300';
            $schedule->command($cmd2)
                // since it's a queue processor, only check every 30 minutes or so
                ->everyThirtyMinutes()
                ->appendOutputTo($search);

            // echo 'starting!'."\n";
        }
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
