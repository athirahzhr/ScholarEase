<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapePetronas extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:petronas';

    /**
     * The console command description.
     */
    protected $description = 'Run Petronas Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running Petronas scraper...');

        exec(
            'node ' . base_path('scrapers/petronas.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ Petronas scraping completed');
        } else {
            $this->error('❌ Khazanah scraping failed');
        }

        return Command::SUCCESS;
    }
}
