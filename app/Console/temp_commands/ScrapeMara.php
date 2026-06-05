<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeMara extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:mara';

    /**
     * The console command description.
     */
    protected $description = 'Run Mara Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running Mara scraper...');

        exec(
            'node ' . base_path('scrapers/mara.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ Mara scraping completed');
        } else {
            $this->error('❌ Mara scraping failed');
        }

        return Command::SUCCESS;
    }
}
