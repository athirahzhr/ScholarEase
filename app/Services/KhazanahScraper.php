<?php

namespace App\Services;

use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;
use Illuminate\Support\Facades\Log;

class KhazanahScraper
{
    public function scrape(): array
    {
        $script = base_path('scrapers/khazanah.js');
        shell_exec("node \"$script\"");

        $path = storage_path('app/khazanah.json');

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    public function saveToDatabase(array $data): int
    {
        $saved = 0;

        foreach ($data as $item) {

            if (
                Scholarship::where('application_link', $item['application_link'])
                    ->where('source_website', 'khazanah')
                    ->exists()
            ) {
                continue;
            }

            // Create scholarship
            $scholarship = Scholarship::create([
                'title'            => $item['title'],
                'description'      => $item['description'] ?? null,
                'eligibility'      => $item['eligibility'] ?? null,
                'raw_eligibility'  => $item['raw_eligibility'] ?? null,
                'coverage'         => $item['coverage'] ?? null,
                'provider'         => 'Yayasan Khazanah',
                'application_link' => $item['application_link'],
                'source'           => 'scraped',
                'source_website'   => 'khazanah',
                'is_active'        => true,
                'is_official'      => true,
                
                // Legacy fields (auto-categorized by JS scraper)
                'academic_category' => $item['academic_category'] ?? null,
                'income_category'   => $item['income_category'] ?? null,
                'study_path'        => $item['study_path'] ?? null,
            ]);

            // ✅ NEW: Auto-create eligibility criteria
            $this->createEligibilityCriteria($scholarship, $item);

            $saved++;
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
            $minAs = match($item['academic_category'] ?? null) {
                'A1' => 0,
                'A2' => 4,
                'A3' => 7,
                'A4' => 10,
                default => null,
            };

            // Extract required subjects from structured data if available
            $requiredSubjects = null;
            if (isset($item['structured_eligibility']['subjects']) && 
                !empty($item['structured_eligibility']['subjects'])) {
                
                $subjects = [];
                foreach ($item['structured_eligibility']['subjects'] as $subject) {
                    // Try to parse "Mathematics: A" or similar
                    if (preg_match('/(.+?):\s*([A-E][+-]?)/i', $subject, $matches)) {
                        $subjects[] = [
                            'subject' => trim($matches[1]),
                            'min_grade' => strtoupper(trim($matches[2])),
                        ];
                    }
                }
                
                if (!empty($subjects)) {
                    $requiredSubjects = json_encode($subjects);
                }
            }

            // Prepare base criteria
            $criteriaData = [
                'scholarship_id' => $scholarship->id,
                
                // Academic
                'min_spm_as' => $minAs ?? 8, // Khazanah typically 8A minimum
                'academic_categories' => $item['academic_category'] 
                    ? json_encode([$item['academic_category']]) 
                    : json_encode(['A3']), // Default to 7-9A
                'required_subjects' => $requiredSubjects,
                
                // Income - Khazanah focuses on B40/M40
                'income_categories' => $item['income_category'] 
                    ? json_encode([$item['income_category']]) 
                    : json_encode(['B1', 'B3']), // Default: B40 and M40
                
                // Study path
                'study_paths' => $item['study_path'] 
                    ? json_encode([$item['study_path']]) 
                    : null,
                
                // Khazanah-specific defaults
                'citizenship_required' => 'Malaysian',
                'max_age' => 18, // Foundation level
                'leadership_priority' => true, // Khazanah values leadership
                'study_destination' => 'Overseas',
                'bond_required' => true,
                
                'notes' => 'Auto-created from Khazanah scraper',
            ];

            // Program-specific adjustments
            $titleLower = strtolower($item['title']);
            
            if (str_contains($titleLower, 'global')) {
                $criteriaData['min_spm_as'] = 8;
                $criteriaData['leadership_required'] = true;
            }
            
            if (str_contains($titleLower, 'watan')) {
                $criteriaData['study_destination'] = 'Local';
                $criteriaData['min_spm_as'] = 7;
            }

            ScholarshipEligibilityCriteria::create($criteriaData);
            
            Log::info("Created eligibility for: {$scholarship->title}");

        } catch (\Exception $e) {
            Log::error("Failed to create eligibility for {$scholarship->title}: {$e->getMessage()}");
        }
    }
}