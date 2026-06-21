<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeAll extends Command
{
    protected $signature = 'scrape:all';
    protected $description = 'Run all scholarship scrapers';

    public function handle()
{
    $this->info('🚀 Running all scholarship scrapers...');

    $output = [];
    $returnCode = 0;

    exec(
    'cd ' . base_path() . ' && npm run scrape:all 2>&1',
    $output,
    $returnCode
    );

    foreach ($output as $line) {
        $this->line($line);
    }

    if ($returnCode !== 0) {
        $this->error('❌ Scraping process failed.');
        return Command::FAILURE;
    }

    $this->info('🎉 All scrapers completed successfully.');

    return Command::SUCCESS;
}
}
