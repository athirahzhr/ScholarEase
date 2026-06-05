<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeYP extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrape:yp';

    /**
     * The console command description.
     */
    protected $description = 'Run YP Node.js scraper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Running YP scraper...');

        exec(
            'node ' . base_path('scrapers/yp.js'),
            $output,
            $status
        );

        if ($status === 0) {
            $this->info('✅ YP scraping completed');
        } else {
            $this->error('❌ YP scraping failed');
        }

        return Command::SUCCESS;
    }
}
