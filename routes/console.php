<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule payment reminders - runs daily at 8:00 AM
Schedule::command('payments:send-reminders --days=7')->dailyAt('08:00');

// Send overdue reminders every Monday at 8:00 AM
Schedule::command('payments:send-reminders --days=3 --overdue')->weeklyOn(1, '08:00');
