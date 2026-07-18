<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendDeadlineNotifications::class,
        \App\Console\Commands\ImportScholarships::class,
        \App\Console\Commands\ScrapeJPA::class,
        \App\Console\Commands\ScrapeKhazanah::class,
        \App\Console\Commands\ScrapePetronas::class,
        \App\Console\Commands\ScrapeMara::class,
        \App\Console\Commands\ScrapeBNM::class,
        \App\Console\Commands\ScrapeBPMP::class,
        \App\Console\Commands\ScrapeAxiata::class,
        \App\Console\Commands\ScrapeShell::class,
        \App\Console\Commands\ScrapeYP::class,
        \App\Console\Commands\ScrapeAll::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
    // Scraping
    $schedule->command('scrape:all')
        ->weekly()
        ->mondays()
        ->at('02:00')
        ->when(function () {
            return Carbon::now()->weekOfYear % 2 == 0;
        });

    // Update Scholarship Status
    $schedule->command('scholarships:update-status')
    ->daily()
    ->at('00:05');

    // Deadline Notification
    $schedule->command('notify:scholarship-deadline')
        ->daily()
        ->at('08:00');
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