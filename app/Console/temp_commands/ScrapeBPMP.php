<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeBPMP extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:bpmp';

    /**
     * The console command description.
     */
    protected $description = 'Run BPMP Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running BPMP scraper...');

        exec(
            'node ' . base_path('scrapers/bpmp.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ BPMP scraping completed');
        } else {
            $this->error('❌ BPMP scraping failed');
        }

        return Command::SUCCESS;
    }
}
