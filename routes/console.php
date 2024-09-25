<?php

use App\Jobs\Gsubz\FetchDataPlans;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new FetchDataPlans())->everyMinute()->emailOutputOnFailure(config("mail.app_support"));
