<?php

use App\Jobs\SendDailySummaryNotifications;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendDailySummaryNotifications)
    ->timezone('Asia/Kolkata')
    ->dailyAt('11:05');