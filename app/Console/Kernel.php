<?php

namespace App\Console;

use App\Console\Commands\Cloudpayments\UpdateNotifications;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\Cloudpayments\UpdatePaymentStatus;
use App\Console\Commands\Statistics\UpdateStatistics;
use App\Console\Commands\Cloudpayments\UpdateSubscription;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdatePaymentStatus::class,
        UpdateSubscription::class,
        UpdateNotifications::class,
        UpdateStatistics::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update:notifications')->everyTenMinutes();
        // $schedule->command('cloudpayments:update:payment_status')->everyTwoMinutes();
        $schedule->command('cloudpayments:update:subscription')->hourly();
        $schedule->command('update:statistics')->everySixHours();
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
