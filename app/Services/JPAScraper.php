<?php

namespace App\Services;

use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;
use Illuminate\Support\Facades\Log;

class JpaScraper
{
    public function scrape(): array
    {
        $script = base_path('scrapers/jpa.js');
        shell_exec("node \"$script\"");

        $path = storage_path('app/jpa.json');

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    public function saveToDatabase(array $data): int
    {
        $saved = 0;

        foreach ($data as $item) {

            /* ============================
               1️⃣ BASIC VALIDATION
            ============================= */

            // Ensure link is real JPA page
            if (
                empty($item['application_link']) ||
                !str_contains($item['application_link'], 'penajaan.jpa.gov.my')
            ) {
                continue;
            }

            // Skip broken / 404 pages
            if (
                isset($item['description']) &&
                str_contains($item['description'], '404')
            ) {
                Log::warning('Skipped invalid JPA page', [
                    'title' => $item['title'] ?? 'UNKNOWN',
                    'link'  => $item['application_link'],
                ]);
                continue;
            }

            /* ============================
               2️⃣ DUPLICATE PREVENTION
               (application_link is safest)
            ============================= */

            if (
                Scholarship::where('application_link', $item['application_link'])
                    ->where('source_website', 'jpa')
                    ->exists()
            ) {
                continue;
            }

            /* ============================
               3️⃣ CREATE SCHOLARSHIP
            ============================= */

            try {
                $scholarship = Scholarship::create([
                    'title'            => $item['title'],
                    'description'      => $item['description'] ?? null,
                    'eligibility'      => $item['eligibility'] ?? null,
                    'raw_eligibility'  => $item['raw_eligibility'] ?? null,
                    'provider'         => 'Jabatan Perkhidmatan Awam',
                    'application_link' => $item['application_link'],
                    'source'           => 'scraped',
                    'source_website'   => 'jpa',
                    'is_active'        => true,
                    'is_official'      => true,

                    // Legacy classification
                    'academic_category' => $item['academic_category'] ?? null,
                    'income_category'   => $item['income_category'] ?? null,
                    'study_path'        => $item['study_path'] ?? null,
                ]);

                // Auto-create structured eligibility
                $this->createEligibilityCriteria($scholarship, $item);

                $saved++;

            } catch (\Throwable $e) {
                Log::error('Failed to save JPA scholarship', [
                    'title' => $item['title'] ?? 'UNKNOWN',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $saved;
    }

    /**
     * Create eligibility criteria from scraped data
     */
    protected function createEligibilityCriteria(Scholarship $scholarship, array $item): void
    {
        try {
            // Convert academic category to min_spm_as
            $minAs = match ($item['academic_category'] ?? null) {
                'A1' => 0,
                'A2' => 4,
                'A3' => 7,
                'A4' => 10,
                default => null,
            };

            $criteriaData = [
                'scholarship_id' => $scholarship->id,

                // Academic
                'min_spm_as' => $minAs,
                'academic_categories' => $item['academic_category']
                    ? json_encode([$item['academic_category']])
                    : null,

                // Income
                'income_categories' => $item['income_category']
                    ? json_encode([$item['income_category']])
                    : null,

                // Study path
                'study_paths' => $item['study_path']
                    ? json_encode([$item['study_path']])
                    : null,

                // Defaults
                'citizenship_required' => 'Malaysian',
                'max_age' => 19,
                'notes' => 'Auto-created from JPA scraper',
            ];

            /* ============================
               PROGRAM-SPECIFIC LOGIC
            ============================= */

            $title = strtolower($item['title']);

            // Dermasiswa / B40
            if (str_contains($title, 'b40') || str_contains($title, 'dermasiswa')) {
                $criteriaData['income_categories'] = json_encode(['B1']);
                $criteriaData['max_monthly_income'] = 4850.00;
                $criteriaData['study_paths'] = json_encode(['C2', 'C4']);
            }

            // PPN / Overseas
            if (str_contains($title, 'ppn') || str_contains($title, 'nasional')) {
                $criteriaData['min_spm_as'] = 9;
                $criteriaData['study_destination'] = 'Overseas';
                $criteriaData['bond_required'] = true;
                $criteriaData['income_categories'] = json_encode(['B1', 'B3']);
            }

            // LSPM / Local
            if (str_contains($title, 'lspm') || str_contains($title, 'dalam negara')) {
                $criteriaData['study_destination'] = 'Local';
                $criteriaData['bond_required'] = true;
            }

            ScholarshipEligibilityCriteria::create($criteriaData);

            Log::info("Eligibility created for JPA: {$scholarship->title}");

        } catch (\Throwable $e) {
            Log::error("Eligibility creation failed", [
                'scholarship' => $scholarship->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
