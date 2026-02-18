<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Schedule database backups and other recurring tasks.
|
*/

// Database backup - runs every 6 hours (spec: Section 13)
Schedule::command('db:backup --compress --keep=28')
    ->cron('0 */6 * * *')
    ->withoutOverlapping()
    ->onOneServer();

// Expire overbooking requests that have been pending for more than 2 hours (spec: Section 6.2)
Schedule::command('bookings:expire-overbooking')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

// Daily booking recap email to admin at 23:00
Schedule::command('bookings:daily-recap')
    ->dailyAt('23:00')
    ->withoutOverlapping()
    ->onOneServer();
