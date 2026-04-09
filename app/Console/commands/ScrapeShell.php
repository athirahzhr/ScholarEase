<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeShell extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:shell';

    /**
     * The console command description.
     */
    protected $description = 'Run Shell Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running Shell scraper...');

        exec(
            'node ' . base_path('scrapers/shell.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ Shell scraping completed');
        } else {
            $this->error('❌ Shell scraping failed');
        }

        return Command::SUCCESS;
    }
}
