<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeJPA extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:jpa';

    /**
     * The console command description.
     */
    protected $description = 'Run JPA Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running JPA scraper...');

        exec(
            'node ' . base_path('scrapers/jpa.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ JPA scraping completed');
        } else {
            $this->error('❌ JPA scraping failed');
        }

        return Command::SUCCESS;
    }
}
