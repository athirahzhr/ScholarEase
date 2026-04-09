<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeBNM extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:bnm';

    /**
     * The console command description.
     */
    protected $description = 'Run BNM Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running BNM scraper...');

        exec(
            'node ' . base_path('scrapers/bnm.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ BNM scraping completed');
        } else {
            $this->error('❌ BNM scraping failed');
        }

        return Command::SUCCESS;
    }
}
