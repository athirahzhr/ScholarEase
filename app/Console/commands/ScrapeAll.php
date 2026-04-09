<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeAll extends Command
{
    protected $signature = 'scrape:all';
    protected $description = 'Run all scholarship scrapers';

    public function handle()
    {
        $commands = [
            'scrape:jpa',
            'scrape:khazanah',
            'scrape:petronas',
            'scrape:mara',
            'scrape:bnm',
            'scrape:bpmp',
            'scrape:axiata',
            'scrape:shell',
            'scrape:yp',
        ];

        foreach ($commands as $cmd) {
            $this->info("▶ Running {$cmd}");
            $this->call($cmd);
        }

        $this->info('🎉 All scrapers completed');
        return Command::SUCCESS;
    }
}
