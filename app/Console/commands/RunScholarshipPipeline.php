<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UnienrolScraper;
use App\Services\JpaScraper;
use App\Services\KhazanahScraper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ScrapingLog;
use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;

class RunScholarshipPipeline extends Command
{
    protected $signature = 'pipeline:scholarships 
                            {--verify : Verify stored procedure after scraping}
                            {--migrate : Migrate old scholarships to new eligibility system}';
    
    protected $description = 'Run scholarship scraping & normalization pipeline';

    public function handle()
    {
        $this->info('🚀 Starting Scholarship Data Pipeline');
        $this->newLine();

        // STEP 1: VERIFY DATABASE SETUP (NEW)
        if ($this->option('verify')) {
            $this->verifyDatabaseSetup();
            $this->newLine();
        }

        // STEP 2: SCRAPING + AUTO-ELIGIBILITY CREATION
        $this->info('📥 SCRAPING SCHOLARSHIPS');
        $this->runScraper(JpaScraper::class, 'JPA', 'https://penajaan.jpa.gov.my');
        $this->runScraper(KhazanahScraper::class, 'Yayasan Khazanah', 'https://www.yayasankhazanah.com.my');
        $this->runScraper(UnienrolScraper::class, 'Unienrol', 'https://unienrol.com');
        $this->newLine();

        // STEP 3: MIGRATE OLD SCHOLARSHIPS (OPTIONAL)
        if ($this->option('migrate')) {
            $this->migrateOldScholarships();
            $this->newLine();
        }

        // STEP 4: DATA HEALTH CHECK
        $this->info('🔍 HEALTH CHECK');
        $this->checkEligibilityCoverage();
        $this->checkCategorizationCoverage();
        $this->checkStoredProcedure();
        $this->newLine();

        // STEP 5: SUMMARY REPORT
        $this->displaySummary();

        $this->info('🎉 Pipeline completed successfully');
        return Command::SUCCESS;
    }

    /**
     * Verify database setup for new system
     */
    protected function verifyDatabaseSetup()
    {
        $this->info('🔧 Verifying database setup...');
        
        // Check if eligibility table exists
        try {
            $count = ScholarshipEligibilityCriteria::count();
            $this->info("   ✅ scholarship_eligibility_criteria table exists ({$count} records)");
        } catch (\Exception $e) {
            $this->error('   ❌ scholarship_eligibility_criteria table missing!');
            $this->warn('   Run: mysql < scholarship_database_fix.sql');
            return;
        }

        // Check if stored procedure exists
        try {
            $result = DB::select("SHOW PROCEDURE STATUS WHERE Name = 'find_matching_scholarships' AND Db = DATABASE()");
            if (empty($result)) {
                $this->warn('   ⚠ Stored procedure not found');
                $this->warn('   Run: mysql < create_procedure_fixed.sql');
            } else {
                $this->info('   ✅ Stored procedure exists');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Cannot check stored procedure');
        }
    }

    /**
     * Run a scraper and track results
     */
    protected function runScraper(string $scraperClass, string $name, string $url)
    {
        $this->info("🔹 Scraping {$name}");
        $start = microtime(true);

        // 1️⃣ CREATE LOG (START)
        $log = ScrapingLog::create([
            'website_name'       => $name,
            'website_url'        => $url,
            'status'             => 'in_progress',
            'scholarships_added' => 0,
            'details'            => 'Scraping started',
        ]);

        try {
            // 2️⃣ RUN SCRAPER
            $scraper = app($scraperClass);
            $data    = $scraper->scrape();
            $saved   = $scraper->saveToDatabase($data);

            // 3️⃣ COUNT ELIGIBILITY CREATED
            $withEligibility = Scholarship::where('source_website', strtolower($name))
                ->has('eligibilityCriteria')
                ->count();

            // 4️⃣ UPDATE LOG (SUCCESS)
            $duration = (int)(microtime(true) - $start);
            $log->update([
                'status'             => 'success',
                'scholarships_added' => $saved,
                'details'            => "Scraping completed. {$withEligibility}/{$saved} have eligibility criteria.",
                'duration_seconds'   => $duration,
            ]);

            $this->info("   ✅ {$saved} scholarships saved from {$name}");
            $this->info("   ✅ {$withEligibility} eligibility criteria created");
            $this->info("   ⏱ Duration: {$duration}s");

        } catch (\Throwable $e) {

            // 5️⃣ UPDATE LOG (FAILED)
            $log->update([
                'status'           => 'failed',
                'error_message'    => $e->getMessage(),
                'duration_seconds' => (int)(microtime(true) - $start),
            ]);

            $this->error("   ❌ {$name} scraping failed: {$e->getMessage()}");
            Log::error("Scraper {$name} failed", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check eligibility criteria coverage
     */
    protected function checkEligibilityCoverage()
    {
        $total = Scholarship::where('is_active', true)->count();
        $withEligibility = Scholarship::has('eligibilityCriteria')
            ->where('is_active', true)
            ->count();
        $missing = $total - $withEligibility;

        $percentage = $total > 0 ? round(($withEligibility / $total) * 100) : 0;

        if ($missing > 0) {
            $this->warn("   ⚠ Eligibility Coverage: {$withEligibility}/{$total} ({$percentage}%)");
            $this->warn("   ⚠ {$missing} active scholarships missing eligibility criteria");
            
            if ($missing > 0 && $missing <= 10) {
                $this->info('   💡 Missing scholarships:');
                $missingScholarships = Scholarship::doesntHave('eligibilityCriteria')
                    ->where('is_active', true)
                    ->limit(10)
                    ->get(['id', 'title']);
                
                foreach ($missingScholarships as $s) {
                    $this->line("      - [{$s->id}] {$s->title}");
                }
            }
        } else {
            $this->info("   ✅ Eligibility Coverage: 100% ({$withEligibility}/{$total})");
        }
    }

    /**
     * Check categorization coverage (legacy A, B, C system)
     */
    protected function checkCategorizationCoverage()
    {
        $uncategorized = Scholarship::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('academic_category')
                      ->orWhereNull('income_category')
                      ->orWhereNull('study_path');
            })
            ->count();

        if ($uncategorized > 0) {
            $this->warn("   ⚠ {$uncategorized} scholarships missing legacy categories (A/B/C)");
        } else {
            $this->info('   ✅ All scholarships have legacy categories');
        }
    }

    /**
     * Check if stored procedure exists and works
     */
    protected function checkStoredProcedure()
    {
        try {
            // Test the stored procedure with sample data
            $result = DB::select('CALL find_matching_scholarships(?, ?, ?, ?, ?, ?, ?, ?)', [
                8, 'B1', 'C1', true, 18, 'Female', 'Selangor', true
            ]);

            $count = count($result);
            $this->info("   ✅ Stored procedure working ({$count} test matches found)");

        } catch (\Exception $e) {
            $this->error('   ❌ Stored procedure not working');
            $this->warn("   Error: {$e->getMessage()}");
            $this->warn('   Run: mysql < create_procedure_fixed.sql');
        }
    }

    /**
     * Migrate old scholarships to new eligibility system
     */
    protected function migrateOldScholarships()
    {
        $this->info('🔄 MIGRATING OLD SCHOLARSHIPS');

        $needsMigration = Scholarship::doesntHave('eligibilityCriteria')
            ->where('is_active', true)
            ->whereNotNull('academic_category')
            ->get();

        if ($needsMigration->isEmpty()) {
            $this->info('   ✅ No scholarships need migration');
            return;
        }

        $this->info("   📊 Found {$needsMigration->count()} scholarships to migrate");
        
        $bar = $this->output->createProgressBar($needsMigration->count());
        $bar->start();

        $migrated = 0;
        foreach ($needsMigration as $scholarship) {
            try {
                $minAs = match($scholarship->academic_category) {
                    'A1' => 0,
                    'A2' => 4,
                    'A3' => 7,
                    'A4' => 10,
                    default => null,
                };

                ScholarshipEligibilityCriteria::create([
                    'scholarship_id' => $scholarship->id,
                    'min_spm_as' => $minAs,
                    'academic_categories' => $scholarship->academic_category 
                        ? json_encode([$scholarship->academic_category]) 
                        : null,
                    'income_categories' => $scholarship->income_category 
                        ? json_encode([$scholarship->income_category]) 
                        : null,
                    'study_paths' => $scholarship->study_path 
                        ? json_encode([$scholarship->study_path]) 
                        : null,
                    'notes' => 'Migrated from legacy categories by pipeline command',
                ]);

                $migrated++;
            } catch (\Exception $e) {
                Log::error("Failed to migrate scholarship {$scholarship->id}: {$e->getMessage()}");
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("   ✅ Migrated {$migrated}/{$needsMigration->count()} scholarships");
    }

    /**
     * Display summary report
     */
    protected function displaySummary()
    {
        $this->info('📊 SUMMARY REPORT');
        
        $stats = [
            'Total Active Scholarships' => Scholarship::where('is_active', true)->count(),
            'With Eligibility Criteria' => ScholarshipEligibilityCriteria::count(),
            'Scraped (JPA)' => Scholarship::where('source_website', 'jpa')->count(),
            'Scraped (Khazanah)' => Scholarship::where('source_website', 'khazanah')->count(),
            'Scraped (UniEnrol)' => Scholarship::where('source_website', 'unienrol')->count(),
            'Manual Entries' => Scholarship::where('source', 'manual')->count(),
            'Recent Scrapes (24h)' => ScrapingLog::where('created_at', '>=', now()->subDay())->count(),
        ];

        foreach ($stats as $label => $count) {
            $this->line("   {$label}: {$count}");
        }
    }
}