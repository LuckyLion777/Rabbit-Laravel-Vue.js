<?php

namespace App\Console;

use App\Console\Commands\CreditReferrers;
use App\Console\Commands\FixAssociations;
use App\Console\Commands\MarkOriginalEvents;
use App\Console\Commands\NotifyAboutUpcoming;
use App\Console\Commands\RecoverWeightingEvents;
use App\Console\Commands\RefreshSubscriptions;
use App\Console\Commands\RemoveArchivedNotClosedEvents;
use App\Console\Commands\TrialExpiration;
use App\Console\Commands\UpdateCustomers;
use App\Console\Commands\WeeklyDigest;
use App\Console\Commands\RemoveTempFiles;
use App\Console\Commands\UpdateRecurringEvents;
use App\Console\Commands\UploadAssetsToCloudinary;
use App\Console\Commands\AddArchivedAtDateToLitter;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FixLitterWeights;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        RemoveTempFiles::class,
        UpdateRecurringEvents::class,
        UploadAssetsToCloudinary::class,
        AddArchivedAtDateToLitter::class,
        FixAssociations::class,
        WeeklyDigest::class,
        FixLitterWeights::class,
        TrialExpiration::class,
        CreditReferrers::class,
        RefreshSubscriptions::class,
        UpdateCustomers::class,
        RemoveArchivedNotClosedEvents::class,
        NotifyAboutUpcoming::class,
        RecoverWeightingEvents::class,
        MarkOriginalEvents::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
//        $schedule->command('removeTempFiles')
//            ->dailyAt('00:01');
//        $schedule->command('updateRecurringEvents')
//            ->weeklyOn(1,'00:01');
        $schedule->command('notify:upcoming')->dailyAt('07:00');
        $schedule->command('mail:digest')
            ->dailyAt('07:00');
        $schedule->command('mail:trial-expiration')
            ->hourly();
        $schedule->command('referrers:credit')
            ->everyFiveMinutes();
        $schedule->command('subscriptions:refresh')
            ->everyFiveMinutes();
    }
}
