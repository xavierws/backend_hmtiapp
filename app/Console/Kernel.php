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
            $sekarang = date("Y-m-d H:i:s");
            $besok = date("Y-m-d H:i:s",strtotime($sekarang."+ 2 days"));
            $events = Event::where('kategori','!=','canceled')->where('start_date','>=',$sekarang)->where('start_date','<=',$besok)->orderBy('start_date')->get();

            foreach ($events as $event) {
                /*
                  $startDate = \Carbon\Carbon::parse($event->start_date);
                  $currentTime = \Carbon\Carbon::parse(now());
                  $diff = $startDate->diffInDays($currentTime);
                  if ($diff == 1) {
                      PushNotification::handle('Reminder', $event->name);
                  }
                  */
                PushNotification::handle('Reminder', $event->name);
                /*
                $startDate = date_create("2013-03-15");
                $currentTime = date_create("2013-03-16");
                $diff = date_diff($startDate,$currentTime);
                if ($diff->format("%R%a") == "+1") {
                  PushNotification::handle('Reminder', "asdasdas");
                 }
                 */
            }
        })->everyMinute();
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
