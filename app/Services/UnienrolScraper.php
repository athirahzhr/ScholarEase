<?php

namespace App\Services;

use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;
use Illuminate\Support\Facades\Log;

class UnienrolScraper
{
    public function scrape(): array
    {
        // 1️⃣ Run the Node scraper
        $script = base_path('scrapers/unienrol.js');
        shell_exec("node \"$script\"");

        // 2️⃣ Read JSON file written by Node
        $jsonPath = storage_path('app/unienrol.json');

        if (!file_exists($jsonPath)) {
            return [];
        }

        $json = file_get_contents($jsonPath);

        return json_decode($json, true) ?? [];
    }

    public function saveToDatabase(array $data): int
    {
        $saved = 0;

        foreach ($data as $item) {

            $exists = Scholarship::where('application_link', $item['application_link'])
                ->exists();

            if ($exists) {
                continue; // prevent duplicates
            }

            // Get provider from scraped data or guess from title
            $provider = $item['provider'] ?? $this->guessProvider($item['title']);

            // Create scholarship
            $scholarship = Scholarship::create([
                'title'            => $item['title'],
                'description'      => $item['description'] ?? null,
                'eligibility'      => $item['eligibility'] ?? null,
                'raw_eligibility'  => $item['eligibility'] ?? null,
                'coverage'         => $item['coverage'] ?? null,
                'provider'         => $provider,
                'application_link' => $item['application_link'],
                'source'           => 'scraped',
                'is_active'        => true,
                'is_official'      => false,
                'source_website'   => 'unienrol',
                
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

            // Extract subjects from structured eligibility if available
            $requiredSubjects = null;
            if (isset($item['structured_eligibility']['subjects']) && 
                !empty($item['structured_eligibility']['subjects'])) {
                
                $subjects = [];
                foreach ($item['structured_eligibility']['subjects'] as $subject) {
                    // Parse patterns like "Mathematics A" or "English: A+"
                    if (preg_match('/(.+?)[:=]?\s*([A-E][+-]?)/i', $subject, $matches)) {
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

            // Detect demographics from structured data
            $bumiputeraRequired = false;
            $genderRequirement = 'Any';
            $maxAge = null;

            if (isset($item['structured_eligibility']['demographic'])) {
                foreach ($item['structured_eligibility']['demographic'] as $demo) {
                    $lower = strtolower($demo);
                    
                    if (str_contains($lower, 'bumiputera')) {
                        $bumiputeraRequired = true;
                    }
                    
                    if (str_contains($lower, 'female') || str_contains($lower, 'women')) {
                        $genderRequirement = 'Female';
                    }
                    
                    if (preg_match('/(\d+)\s*years?\s*old/i', $demo, $matches)) {
                        $maxAge = (int)$matches[1];
                    }
                }
            }

            // Detect leadership requirements
            $leadershipRequired = false;
            $leadershipPriority = false;
            
            if (isset($item['structured_eligibility']['activities'])) {
                foreach ($item['structured_eligibility']['activities'] as $activity) {
                    $lower = strtolower($activity);
                    
                    if (str_contains($lower, 'leadership')) {
                        if (str_contains($lower, 'required') || str_contains($lower, 'must')) {
                            $leadershipRequired = true;
                        } else {
                            $leadershipPriority = true;
                        }
                    }
                }
            }

            // Prepare criteria data
            $criteriaData = [
                'scholarship_id' => $scholarship->id,
                
                // Academic
                'min_spm_as' => $minAs,
                'academic_categories' => $item['academic_category'] 
                    ? json_encode([$item['academic_category']]) 
                    : null,
                'required_subjects' => $requiredSubjects,
                
                // Demographic
                'bumiputera_required' => $bumiputeraRequired,
                'gender_requirement' => $genderRequirement,
                'max_age' => $maxAge,
                
                // Income
                'income_categories' => $item['income_category'] 
                    ? json_encode([$item['income_category']]) 
                    : null,
                
                // Study
                'study_paths' => $item['study_path'] 
                    ? json_encode([$item['study_path']]) 
                    : null,
                
                // Activities
                'leadership_required' => $leadershipRequired,
                'leadership_priority' => $leadershipPriority,
                
                // Default
                'citizenship_required' => 'Malaysian',
                
                'notes' => 'Auto-created from UniEnrol scraper',
            ];

            // Provider-specific adjustments
            $providerLower = strtolower($scholarship->provider);
            
            if (str_contains($providerLower, 'jpa')) {
                $criteriaData['max_age'] = 19;
                $criteriaData['bond_required'] = true;
            }
            
            if (str_contains($providerLower, 'petronas')) {
                $criteriaData['bond_required'] = true;
                $criteriaData['study_paths'] = json_encode(['C1', 'C3']);
            }
            
            if (str_contains($providerLower, 'bank negara') || str_contains($providerLower, 'khazanah')) {
                $criteriaData['leadership_priority'] = true;
                $criteriaData['bond_required'] = true;
            }

            ScholarshipEligibilityCriteria::create($criteriaData);
            
            Log::info("Created eligibility for: {$scholarship->title}");

        } catch (\Exception $e) {
            Log::error("Failed to create eligibility for {$scholarship->title}: {$e->getMessage()}");
        }
    }

    /**
     * Guess provider from scholarship title
     */
    private function guessProvider(string $title): string
    {
        if (str_contains($title, 'JPA')) return 'Jabatan Perkhidmatan Awam';
        if (str_contains($title, 'MARA')) return 'MARA';
        if (str_contains($title, 'PETRONAS')) return 'PETRONAS';
        if (str_contains($title, 'Shell')) return 'Shell Malaysia';
        if (str_contains($title, 'CIMB')) return 'CIMB';
        if (str_contains($title, 'Bank Negara')) return 'Bank Negara Malaysia';
        if (str_contains($title, 'Khazanah')) return 'Yayasan Khazanah';
        if (str_contains($title, 'UEM')) return 'Yayasan UEM';
        if (str_contains($title, 'Sarawak Energy')) return 'Sarawak Energy';
        if (str_contains($title, 'TNB') || str_contains($title, 'Tenaga')) return 'TNB';
        if (str_contains($title, 'Sime Darby')) return 'Sime Darby Foundation';

        return 'External Provider';
    }
}