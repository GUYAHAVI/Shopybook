<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh Kenyan market intelligence cache every day at 3 AM
Schedule::command('ai:fetch-market-insights')->dailyAt('03:00');
