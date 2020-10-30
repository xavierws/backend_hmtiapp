<?php

namespace App\Console;

use App\Actions\PushNotification;
use App\Models\Event;
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
//         $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            $events = Event::all();

            foreach ($events as $event) {
                $startDate = \Carbon\Carbon::parse($event->start_date);
                $currentTime = \Carbon\Carbon::parse(now());

                $diff = $startDate->diffInDays($currentTime);

                if ($diff == 1) {
                    PushNotification::handle('Reminder', $event->name);
                }
            }
        })->daily()->at('13:00');
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
