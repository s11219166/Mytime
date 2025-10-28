<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule project due date checks
// Morning run at 9:00 AM - for all notification types
Schedule::command('projects:check-due-dates')
    ->dailyAt('09:00')
    ->name('morning-due-date-check')
    ->emailOutputOnFailure(config('mail.admin_email'));

// Evening run at 6:00 PM - for 3-day reminders (second reminder)
Schedule::command('projects:check-due-dates')
    ->dailyAt('18:00')
    ->name('evening-due-date-check')
    ->emailOutputOnFailure(config('mail.admin_email'));
