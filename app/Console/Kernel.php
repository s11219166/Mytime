<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check project due dates every day at 9:00 AM
        $schedule->command('projects:check-due-dates')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->onOneServer();

        // Also check at 6:00 PM for evening reminders
        $schedule->command('projects:check-due-dates')
            ->dailyAt('18:00')
            ->withoutOverlapping()
            ->onOneServer();

        // Optional: Check every hour for critical alerts (overdue projects)
        $schedule->command('projects:check-due-dates')
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
