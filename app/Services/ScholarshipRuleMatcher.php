<?php

namespace App\Services;

/**
 * Rule-Based Scholarship Matcher
 * ================================
 * FYP — Pure Rule-Based Recommendation System
 *
 * DESIGN PHILOSOPHY:
 * ─────────────────────────────────────────────────────
 * Every criterion is evaluated as a binary pass/fail rule.
 * No scoring weights are applied — no percentage, no points.
 * This reflects the actual policy language used by Malaysian
 * scholarship providers and requires no external justification
 * for weight values.
 *
 * A scholarship either matches the student or it does not.
 * The only exception is income keutamaan (preference) — where
 * the student is still shown but flagged as "Less Priority"
 * if they are outside the preferred income group.
 *
 * ─────────────────────────────────────────────────────
 * HARD FILTERS (all pass/fail — any fail = excluded):
 *
 *   1. Citizenship        — must match if required
 *   2. Bumiputera         — must be Bumiputera if required
 *   3. Study level        — must be in allowed list
 *   4. Field of study     — must be in allowed list
 *   5. Age                — must be within min/max range
 *   6. SPM result         — shortfall must not exceed 2As
 *   7. Income RM ceiling  — monthly_income ≤ max_monthly_income
 *   8. Income category    — if 1 category ticked (syarat wajib)
 *
 * KEUTAMAAN FLAG (not a filter — informational only):
 *
 *   Income preference     — if 2+ categories ticked
 *   Student in preferred group  → priority = 'keutamaan'
 *   Student outside group       → priority = 'less_priority'
 *
 * SORTING:
 *   1. Keutamaan matches first
 *   2. Less priority matches second
 *   3. Within each group → soonest deadline first
 *
 * ─────────────────────────────────────────────────────
 * INCOME CATEGORY AUTO-RESOLUTION (from checkbox count):
 *
 *   0 ticked  → no restriction   → pass, priority = 'keutamaan'
 *   1 ticked  → hard requirement → must match or excluded
 *   2+ ticked → preference       → pass, flag keutamaan or less_priority
 *
 * INCOME THRESHOLDS (Rafizi Ramli, Ministry of Economy, 2023):
 *   B40 → ≤ RM4,850/month
 *   M40 → RM4,851 – RM10,960/month
 *   T20 → > RM10,960/month
 * ─────────────────────────────────────────────────────
 */
class ScholarshipRuleMatcher
{
    /**
     * SPM near-hard filter tolerance.
     * Shortfall > 2As → excluded (too far from requirement).
     * Shortfall 0, 1, 2 → eligible (close enough to be relevant).
     *
     * Rationale (design decision):
     *   A student 1–2As short may still be competitive if all other
     *   criteria are met. Beyond 2As the gap is too significant to
     *   produce a meaningful recommendation.
     */
    private const SPM_MAX_SHORTFALL = 2;

    /**
     * Official Malaysian household income thresholds.
     * Source: Ministry of Economy Malaysia (Rafizi Ramli, 2023)
     */
    private const INCOME_THRESHOLDS = [
        'B40' => 4850,
        'M40' => 10960,
    ];

    // =========================================================================
    // PUBLIC: Auto-derive income category from actual monthly income (RM)
    // Call on profile save. Store result in student->income_category.
    // =========================================================================

    public static function deriveIncomeCategory(int $monthlyIncome): string
    {
        if ($monthlyIncome <= self::INCOME_THRESHOLDS['B40']) {
            return 'B40';
        }

        if ($monthlyIncome <= self::INCOME_THRESHOLDS['M40']) {
            return 'M40';
        }

        return 'T20';
    }

    // =========================================================================
    // PUBLIC: Get Recommendations
    // =========================================================================

    public function getRecommendations($student, $scholarships): \Illuminate\Support\Collection
    {
        $matched       = [];
        $keutamaan     = [];
        $lessPriority  = [];

        foreach ($scholarships as $scholarship) {
            $criteria = $scholarship->eligibilityCriteria;

            if (!$criteria) {
                continue;
            }

            $result = $this->matchScholarship($student, $criteria);

            if (!$result['eligible']) {
                continue;
            }

            $scholarship->match_breakdown = $result['breakdown'];
            $scholarship->match_level     = $result['match_level'];
            $scholarship->priority        = $result['priority'];

            // Separate into keutamaan and less_priority groups
            if ($result['priority'] === 'less_priority') {
                $lessPriority[] = $scholarship;
            } else {
                $keutamaan[] = $scholarship;
            }
        }

        // Sort each group by soonest deadline first
        $sortByDeadline = function ($a, $b) {
            $da = $a->deadline ? strtotime($a->deadline) : PHP_INT_MAX;
            $db = $b->deadline ? strtotime($b->deadline) : PHP_INT_MAX;
            return $da <=> $db;
        };

        usort($keutamaan,    $sortByDeadline);
        usort($lessPriority, $sortByDeadline);

        // Keutamaan first, then less priority
        return collect(array_merge($keutamaan, $lessPriority))->values();
    }

    // =========================================================================
    // PUBLIC: Match Single Scholarship
    // =========================================================================

    public function matchScholarship($student, $criteria): array
    {
        // ── Hard Filters ──────────────────────────────────────────────────────
        $filters   = $this->runHardFilters($student, $criteria);
        $failedAny = in_array(false, array_column($filters, 'passed'), true);

        if ($failedAny) {
            return [
                'eligible'    => false,
                'priority'    => null,
                'match_level' => 'Not Eligible',
                'breakdown'   => $filters,
            ];
        }

        // ── Keutamaan Flag ────────────────────────────────────────────────────
        $priority = $this->resolveKeutamaan($student, $criteria);

        return [
            'eligible'    => true,
            'priority'    => $priority,
            'match_level' => $priority === 'keutamaan' ? 'Eligible — Keutamaan' : 'Eligible — Less Priority',
            'breakdown'   => $filters,
        ];
    }

    // =========================================================================
    // Hard Filters
    // Each returns ['passed' => bool, 'label' => string, 'reason' => string]
    // =========================================================================

    private function runHardFilters($student, $criteria): array
    {
        return [
            'citizenship' => $this->checkCitizenship($student, $criteria),
            'bumiputera'  => $this->checkBumiputera($student, $criteria),
            'study_level' => $this->checkStudyLevel($student, $criteria),
            'field'       => $this->checkField($student, $criteria),
            'age'         => $this->checkAge($student, $criteria),
            'spm'         => $this->checkSpm($student, $criteria),
            'income'      => $this->checkIncome($student, $criteria),
        ];
    }

    private function checkCitizenship($student, $criteria): array
    {
        if (!$criteria->citizenship_required) {
            return [
                'passed' => true,
                'label'  => 'Citizenship',
                'reason' => 'No citizenship requirement',
            ];
        }

        $passed = strcasecmp($student->citizenship, $criteria->citizenship_required) === 0;

        return [
            'passed' => $passed,
            'label'  => 'Citizenship',
            'reason' => $passed
                ? "Eligible ({$student->citizenship})"
                : "Required {$criteria->citizenship_required}, student is {$student->citizenship}",
        ];
    }

    private function checkBumiputera($student, $criteria): array
    {
        if (!$criteria->bumiputera_required) {
            return [
                'passed' => true,
                'label'  => 'Bumiputera',
                'reason' => 'No Bumiputera requirement',
            ];
        }

        $passed = (bool) $student->bumiputera;

        return [
            'passed' => $passed,
            'label'  => 'Bumiputera',
            'reason' => $passed
                ? 'Bumiputera status confirmed'
                : 'This scholarship requires Bumiputera status',
        ];
    }

    private function checkStudyLevel($student, $criteria): array
    {
        $allowed = $criteria->study_paths ?? [];

        if (empty($allowed)) {
            return [
                'passed' => true,
                'label'  => 'Study level',
                'reason' => 'No study level restriction',
            ];
        }

        $passed = in_array($student->study_level, $allowed, true);

        return [
            'passed' => $passed,
            'label'  => 'Study level',
            'reason' => $passed
                ? "Eligible ({$student->study_level})"
                : "{$student->study_level} is not in allowed levels: " . implode(', ', $allowed),
        ];
    }

    private function checkField($student, $criteria): array
    {
        $allowed = $criteria->fields_of_study ?? [];

        if (empty($allowed)) {
            return [
                'passed' => true,
                'label'  => 'Field of study',
                'reason' => 'No field restriction',
            ];
        }

        $passed = in_array($student->field_of_study, $allowed, true);

        return [
            'passed' => $passed,
            'label'  => 'Field of study',
            'reason' => $passed
                ? "Eligible ({$student->field_of_study})"
                : "{$student->field_of_study} is not in allowed fields",
        ];
    }

    private function checkAge($student, $criteria): array
    {
        $minOk = $criteria->min_age === null || $student->age >= $criteria->min_age;
        $maxOk = $criteria->max_age === null || $student->age <= $criteria->max_age;
        $passed = $minOk && $maxOk;

        if ($criteria->min_age === null && $criteria->max_age === null) {
            return [
                'passed' => true,
                'label'  => 'Age',
                'reason' => 'No age requirement',
            ];
        }

        $range = ($criteria->min_age ?? '?') . '–' . ($criteria->max_age ?? '?') . ' years';

        return [
            'passed' => $passed,
            'label'  => 'Age',
            'reason' => $passed
                ? "Within range ({$student->age} years, range {$range})"
                : "Age {$student->age} is outside required range {$range}",
        ];
    }

    /**
     * SPM near-hard filter.
     * Shortfall > SPM_MAX_SHORTFALL (2) → excluded.
     * 0–2As short → eligible.
     *
     * Rationale (design decision, no external source required):
     *   Students within 2As of the requirement are close enough to
     *   be a relevant recommendation. Beyond that the academic gap
     *   is too large to be meaningful.
     */
    private function checkSpm($student, $criteria): array
    {
        if ($criteria->min_spm_as === null) {
            return [
                'passed' => true,
                'label'  => 'SPM result',
                'reason' => 'No SPM requirement',
            ];
        }

        $shortfall = $criteria->min_spm_as - $student->total_as;

        if ($shortfall <= 0) {
            return [
                'passed' => true,
                'label'  => 'SPM result',
                'reason' => "Meets requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)",
            ];
        }

        if ($shortfall <= self::SPM_MAX_SHORTFALL) {
            return [
                'passed' => true,
                'label'  => 'SPM result',
                'reason' => "{$shortfall}A short of requirement ({$student->total_as}A / {$criteria->min_spm_as}A required) — within tolerance",
            ];
        }

        return [
            'passed' => false,
            'label'  => 'SPM result',
            'reason' => "{$shortfall}As short of requirement ({$student->total_as}A / {$criteria->min_spm_as}A required) — exceeds tolerance of " . self::SPM_MAX_SHORTFALL,
        ];
    }

    /**
     * Income hard filter.
     *
     * Checks in this order:
     *
     * 1. max_monthly_income set → compare actual RM (syarat wajib)
     * 2. 1 category ticked     → compare derived category (syarat wajib)
     * 3. 2+ categories ticked  → pass here (handled as keutamaan flag)
     * 4. Nothing set           → pass (no restriction)
     */
    private function checkIncome($student, $criteria): array
    {
        // ── RM ceiling ────────────────────────────────────────────────────────
        if ($criteria->max_monthly_income !== null) {
            $passed = $student->monthly_income <= $criteria->max_monthly_income;

            return [
                'passed' => $passed,
                'label'  => 'Monthly income',
                'reason' => $passed
                    ? "Within limit (RM{$student->monthly_income} ≤ RM{$criteria->max_monthly_income})"
                    : "Exceeds limit (RM{$student->monthly_income} > RM{$criteria->max_monthly_income})",
            ];
        }

        // ── Category-based ────────────────────────────────────────────────────
        $categories  = $criteria->income_categories ?? [];
        $incomeType  = $this->resolveIncomeType($categories);

        // 1 ticked → hard requirement
        if ($incomeType === 'requirement') {
            $required        = array_map('strtoupper', $categories);
            $studentCategory = strtoupper($student->income_category ?? '');
            $passed          = in_array($studentCategory, $required, true);

            return [
                'passed' => $passed,
                'label'  => 'Income category',
                'reason' => $passed
                    ? "Eligible ({$studentCategory} — required: " . implode(', ', $required) . ')'
                    : "{$studentCategory} does not meet requirement: " . implode(', ', $required),
            ];
        }

        // 2+ ticked (preference) or 0 ticked (no restriction) → pass here
        return [
            'passed' => true,
            'label'  => 'Income category',
            'reason' => $incomeType === 'preference'
                ? 'Income preference — eligibility determined by keutamaan flag'
                : 'No income restriction',
        ];
    }

    // =========================================================================
    // Keutamaan Flag (not a filter — informational sorting signal only)
    // =========================================================================

    /**
     * Resolve keutamaan priority for scholarships with 2+ income categories.
     *
     * Called only AFTER all hard filters pass.
     *
     * Returns:
     *   'keutamaan'     → student is in preferred income group
     *   'less_priority' → student is outside preferred group (but still eligible)
     *   'keutamaan'     → default for all non-preference scholarships (no penalty)
     */
    private function resolveKeutamaan($student, $criteria): string
    {
        $categories = $criteria->income_categories ?? [];
        $incomeType = $this->resolveIncomeType($categories);

        if ($incomeType !== 'preference') {
            return 'keutamaan'; // no preference = full priority
        }

        $preferred       = array_map('strtoupper', $categories);
        $studentCategory = strtoupper($student->income_category ?? '');

        return in_array($studentCategory, $preferred, true)
            ? 'keutamaan'
            : 'less_priority';
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Auto-resolve income type from number of ticked categories.
     *
     * 0 ticked  → null          (no restriction)
     * 1 ticked  → 'requirement' (syarat wajib — hard filter)
     * 2+ ticked → 'preference'  (keutamaan — flag only)
     */
    private function resolveIncomeType(array $categories): ?string
    {
        $count = count($categories);

        if ($count === 0) return null;
        if ($count === 1) return 'requirement';
        return 'preference';
    }
}