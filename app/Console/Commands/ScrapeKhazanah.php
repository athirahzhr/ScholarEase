<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeKhazanah extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:khazanah';

    /**
     * The console command description.
     */
    protected $description = 'Run Khazanah Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running Khazanah scraper...');

        exec(
            'node ' . base_path('scrapers/khazanah.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ Khazanah scraping completed');
        } else {
            $this->error('❌ Khazanah scraping failed');
        }

        return Command::SUCCESS;
    }
}
