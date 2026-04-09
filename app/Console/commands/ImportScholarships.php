<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ImportScholarships extends Command
{
    protected $signature = 'app:import-scholarships {filename}';
    protected $description = 'Import scraped scholarships JSON into database';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = base_path("scrapers/output/{$filename}.json");

        if (!File::exists($path)) {
            $this->error("❌ File {$filename}.json tak jumpa");
            return Command::FAILURE;
        }

        $data = json_decode(File::get($path), true);

        if (!is_array($data)) {
            $this->error("❌ JSON format rosak");
            return Command::FAILURE;
        }

        $this->info("📥 Importing " . count($data) . " records...");

        foreach ($data as $i => $item) {
            DB::beginTransaction();

            try {
                /* =========================
                 * 1️⃣ INSERT SCHOLARSHIP
                 * ========================= */
                $scholarship = Scholarship::create([
                    'title'            => $item['title'],
                    'provider'         => $item['provider'] ?? 'Unknown',
                    'description'      => $item['title'],
                    'raw_eligibility'  => $item['raw_eligibility'] ?? null,
                    'application_link' => $item['application_link'] ?? null,
                    'deadline'         => $item['application_deadline'] ?? null,
                    'source'           => $item['source'] ?? 'scraped',
                    'source_website'   => $item['source_website'] ?? $filename,
                    'is_official'      => 1,
                    'is_active'        => 1,
                ]);

                /* =========================
                 * 2️⃣ INSERT ELIGIBILITY
                 * ========================= */
                if (!empty($item['rules'])) {
                    ScholarshipEligibilityCriteria::create([
                        'scholarship_id'       => $scholarship->id,

                        'min_spm_as'           => $item['rules']['min_spm_as'] ?? null,
                        'max_spm_as'           => $item['rules']['max_spm_as'] ?? null,

                        'academic_categories' => $item['rules']['academic_categories'] ?? null,
                        'required_subjects'   => $item['rules']['required_subjects'] ?? null,

                        'income_categories'   => $item['rules']['income_categories'] ?? null,
                        'income_strict'       => $item['rules']['income_strict'] ?? true,

                        'study_paths'         => $item['rules']['study_paths'] ?? null,
                        'study_path_strict'   => true,

                        'fields_of_study'     => $item['rules']['fields_of_study'] ?? null,
                        'study_destination'   => 'Both',

                        'bumiputera_required' => $item['rules']['bumiputera_required'] ?? false,
                        'bumiputera_priority' => $item['rules']['bumiputera_priority'] ?? false,

                        'gender_requirement'  => $item['rules']['gender_requirement'] ?? 'Any',
                        'citizenship_required'=> $item['rules']['citizenship_required'] ?? null,
                        'state_requirement'   => $item['rules']['state_requirement'] ?? null,

                        'rural_priority'      => $item['rules']['rural_priority'] ?? false,

                        'min_age'             => $item['rules']['min_age'] ?? null,
                        'max_age'             => $item['rules']['max_age'] ?? null,

                        'leadership_required' => $item['rules']['leadership_required'] ?? false,
                        'leadership_priority' => $item['rules']['leadership_priority'] ?? false,

                        'sports_achievement'  => $item['rules']['sports_achievement'] ?? false,
                        'min_community_hours' => $item['rules']['min_community_hours'] ?? null,

                        'bond_required'       => $item['rules']['bond_required'] ?? false,
                        'bond_years'          => $item['rules']['bond_years'] ?? null,

                        'priority_weight'     => $item['rules']['priority_weight'] ?? 1,
                        'match_all_criteria'  => true,
                        'max_score'           => 100,

                        'notes'               => $item['rules']['notes'] ?? null,
                    ]);
                }

                DB::commit();
                $this->info("✅ [" . ($i+1) . "] Imported: {$item['title']}");

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("❌ [" . ($i+1) . "] Failed: {$item['title']}");
                $this->line($e->getMessage());
            }
        }

        $this->info("🎉 Import selesai!");
        return Command::SUCCESS;
    }
}
