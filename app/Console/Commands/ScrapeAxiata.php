<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeAxiata extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:axiata';

    /**
     * The console command description.
     */
    protected $description = 'Run Axiata Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running Axiata scraper...');

        exec(
            'node ' . base_path('scrapers/axiata.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ Axiata scraping completed');
        } else {
            $this->error('❌ Axiata scraping failed');
        }

        return Command::SUCCESS;
    }
}
