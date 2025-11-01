<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===== REAL-TIME PROJECT CHECKS =====
// Check project due dates and send notifications + emails EVERY HOUR (closest to 5 hours)
Schedule::command('projects:check-due-dates')
    ->hourly()
    ->name('realtime-project-check')
    ->emailOutputOnFailure(config('mail.admin_email'))
    ->runInBackground();

// ===== DAILY COMPREHENSIVE CHECKS =====
// Morning comprehensive check at 9:00 AM
Schedule::command('projects:check-due-dates')
    ->dailyAt('09:00')
    ->name('morning-comprehensive-check')
    ->emailOutputOnFailure(config('mail.admin_email'));

// Evening reminder check at 6:00 PM
Schedule::command('projects:check-due-dates')
    ->dailyAt('18:00')
    ->name('evening-reminder-check')
    ->emailOutputOnFailure(config('mail.admin_email'));

// Test command for new project notifications
Artisan::command('test:new-project-notification', function () {
    $this->call('test:new-project-notification');
})->purpose('Test new project notification');
