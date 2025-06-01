<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

// Schedule the rental expiration processing to run daily at midnight
Schedule::command('rentals:process-expired')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping()
    ->description('Process expired rental orders and mark them as available');
