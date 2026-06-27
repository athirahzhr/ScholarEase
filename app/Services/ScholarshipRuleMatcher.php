<?php

namespace App\Services;

/**
 * Rule-Based Scholarship Matcher
 * ================================
 * FYP — Pure Rule-Based Recommendation System (ScholarEase)
 *
 * DESIGN PHILOSOPHY:
 * ─────────────────────────────────────────────────────────
 * Every criterion is evaluated as a binary pass/fail rule.
 * No scoring weights, no percentage, no points.
 * Directly reflects the policy language used by Malaysian
 * scholarship providers. No external weight justification needed.
 *
 * ─────────────────────────────────────────────────────────
 * HARD FILTERS (pass/fail — any fail = excluded immediately):
 *
 *   1. Citizenship       Must match if required. Empty = open to all.
 *   2. Bumiputera        Must be Bumiputera if bumiputera_required = true.
 *   3. Study level       Must be in allowed list (Foundation / Matriculation /
 *                        Diploma / Degree / TVET only — no postgraduate).
 *   4. Field of study    Must be in allowed list. Empty = open to all.
 *   5. Age               Must be within min/max range. Not set = no restriction.
 *   6. SPM result        Must meet or exceed min_spm_as. Strict — no tolerance.
 *   7. Income RM ceiling monthly_income ≤ max_monthly_income if set.
 *   8. Income category   If 1 category ticked → syarat wajib hard filter.
 *   9. State             Must be in allowed states. Empty = open to all.
 *
 * ─────────────────────────────────────────────────────────
 * KEUTAMAAN FLAG (not a filter — affects ordering only):
 *
 *   Income categories — auto-resolved from checkbox count:
 *   0 ticked  → no restriction  → Priority Match (no penalty)
 *   1 ticked  → hard filter     → handled in Step 1
 *   2+ ticked → preference      → Priority Match if in list
 *                                  General Match if outside list
 *
 * ─────────────────────────────────────────────────────────
 * SORTING:
 *   1st → Priority Match  (soonest deadline first)
 *   2nd → General Match   (soonest deadline first)
 *
 * ─────────────────────────────────────────────────────────
 * NOT INCLUDED IN MATCHING (shown in scholarship detail only):
 *   Leadership, sports achievement, rural priority
 *   — excluded because scholarship providers typically express
 *     these as preferences, not mandatory requirements.
 *
 * ─────────────────────────────────────────────────────────
 * ALLOWED STUDY LEVELS:
 *   Foundation, Matriculation, Diploma, Degree, TVET
 *   for SPM-based scholarship recommendations.
 *
 * ─────────────────────────────────────────────────────────
 * INCOME THRESHOLDS (Rafizi Ramli, Ministry of Economy, 2023):
 *   B40 → ≤ RM4,850/month
 *   M40 → RM4,851 – RM10,960/month
 *   T20 → > RM10,960/month
 * ─────────────────────────────────────────────────────────
 */
class ScholarshipRuleMatcher
{
    /**
     * Allowed study levels.
     * designed for SPM leavers applying for pre-degree and
     * undergraduate programmes only.
     */
    private const ALLOWED_STUDY_LEVELS = [
        'Foundation',
        'Matriculation',
        'Diploma',
        'Degree',
        'TVET',
    ];

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
    //
    // Example:
    //   deriveIncomeCategory(3000)  → 'B40'
    //   deriveIncomeCategory(7000)  → 'M40'
    //   deriveIncomeCategory(15000) → 'T20'
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
        $priorityMatch = [];
        $generalMatch  = [];

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

            if ($result['priority'] === 'general_match') {
                $generalMatch[] = $scholarship;
            } else {
                $priorityMatch[] = $scholarship;
            }
        }

        // Sort each group by soonest deadline first
        $sortByDeadline = function ($a, $b) {
            $da = $a->deadline ? strtotime($a->deadline) : PHP_INT_MAX;
            $db = $b->deadline ? strtotime($b->deadline) : PHP_INT_MAX;
            return $da <=> $db;
        };

        usort($priorityMatch, $sortByDeadline);
        usort($generalMatch,  $sortByDeadline);

        return collect(array_merge($priorityMatch, $generalMatch))->values();
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
            'match_level' => $priority === 'priority_match'
                ? 'Priority Match'
                : 'General Match',
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
            'state'       => $this->checkState($student, $criteria),
        ];
    }

    /**
     * Filter 1 — Citizenship
     * Must match scholarship's required citizenship.
     * If no requirement set → open to all → pass.
     */
    private function checkCitizenship($student, $criteria): array
    {
        if (!$criteria->citizenship_required) {
            return [
                'passed' => true,
                'label'  => 'Citizenship',
                'reason' => 'No citizenship requirement',
            ];
        }

        $passed = strcasecmp(
            $student->citizenship,
            $criteria->citizenship_required
        ) === 0;

        return [
            'passed' => $passed,
            'label'  => 'Citizenship',
            'reason' => $passed
                ? "Eligible ({$student->citizenship})"
                : "Required: {$criteria->citizenship_required} — Student: {$student->citizenship}",
        ];
    }

    /**
     * Filter 2 — Bumiputera
     * Must be Bumiputera if bumiputera_required = true.
     * If not required → open to all races → pass.
     */
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

    /**
     * Filter 3 — Study Level
     * Must be in scholarship's allowed study paths.
     * If no restriction → pass.
     *
     * Allowed values: Foundation, Matriculation, Diploma, Degree, TVET.
     * Postgraduate levels are not applicable for this system.
     */
    private function checkStudyLevel($student, $criteria): array
    {
        $allowed = array_intersect(
            $criteria->study_paths ?? [],
            self::ALLOWED_STUDY_LEVELS
        );

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
                : "{$student->study_level} is not in allowed levels: "
                    . implode(', ', $allowed),
        ];
    }

    /**
     * Filter 4 — Field of Study
     * Must be in scholarship's allowed fields.
     * If no restriction → pass.
     */
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
                : "{$student->field_of_study} is not in allowed fields: "
                    . implode(', ', $allowed),
        ];
    }

    /**
     * Filter 5 — Age
     * Must be within min/max range.
     * If not set → no restriction → pass.
     */
    private function checkAge($student, $criteria): array
    {
        $hasMin = $criteria->min_age !== null;
        $hasMax = $criteria->max_age !== null;

        if (!$hasMin && !$hasMax) {
            return [
                'passed' => true,
                'label'  => 'Age',
                'reason' => 'No age requirement',
            ];
        }

        $minOk  = !$hasMin || $student->age >= $criteria->min_age;
        $maxOk  = !$hasMax || $student->age <= $criteria->max_age;
        $passed = $minOk && $maxOk;
        $range  = ($criteria->min_age ?? '?') . '–' . ($criteria->max_age ?? '?') . ' years';

        return [
            'passed' => $passed,
            'label'  => 'Age',
            'reason' => $passed
                ? "Within range ({$student->age} years, range {$range})"
                : "Age {$student->age} is outside required range {$range}",
        ];
    }

    /**
     * Filter 6 — SPM Result (strict hard filter)
     * Student's total As must meet or exceed min_spm_as.
     * No tolerance — SV feedback: "if requirement is 8A, student needs 8A."
     * If no requirement → pass.
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

        $passed = $student->total_as >= $criteria->min_spm_as;

        return [
            'passed' => $passed,
            'label'  => 'SPM result',
            'reason' => $passed
                ? "Meets requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)"
                : "Does not meet requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)",
        ];
    }

    /**
     * Filter 7 & 8 — Income
     *
     * Checked in this order:
     *
     * A. max_monthly_income set (RM ceiling — syarat wajib)
     *    → compare student's actual monthly_income against ceiling
     *
     * B. income_categories count = 1 (single category — syarat wajib)
     *    → student's derived income_category must match
     *
     * C. income_categories count = 2+ (preference — keutamaan)
     *    → pass here, handled as keutamaan flag after filters
     *
     * D. Nothing set → no restriction → pass
     */
    private function checkIncome($student, $criteria): array
    {
        // A — RM ceiling
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

        $categories = $criteria->income_categories ?? [];
        $type       = $this->resolveIncomeType($categories);

        // B — Single category requirement
        if ($type === 'requirement') {
            $required        = array_map('strtoupper', $categories);
            $studentCategory = strtoupper($student->income_category ?? '');
            $passed          = in_array($studentCategory, $required, true);

            return [
                'passed' => $passed,
                'label'  => 'Income category',
                'reason' => $passed
                    ? "Eligible ({$studentCategory} — required: "
                        . implode(', ', $required) . ')'
                    : "{$studentCategory} does not meet requirement: "
                        . implode(', ', $required),
            ];
        }

        // C — Preference (2+ categories) or D — no restriction → pass
        return [
            'passed' => true,
            'label'  => 'Income category',
            'reason' => $type === 'preference'
                ? 'Income preference applies — see Priority Match / General Match'
                : 'No income restriction',
        ];
    }

    /**
     * Filter 9 — State
     * Student's state must be in scholarship's allowed states.
     * If no restriction → open to all states → pass.
     *
     * Common use case: state-based scholarships
     * e.g. Yayasan Sarawak, Yayasan Negeri Sembilan
     */
    private function checkState($student, $criteria): array
    {
        $allowed = $criteria->states ?? [];

        if (empty($allowed)) {
            return [
                'passed' => true,
                'label'  => 'State',
                'reason' => 'No state restriction',
            ];
        }

        $passed = in_array($student->state, $allowed, true);

        return [
            'passed' => $passed,
            'label'  => 'State',
            'reason' => $passed
                ? "Eligible ({$student->state})"
                : "{$student->state} is not in required states: "
                    . implode(', ', $allowed),
        ];
    }

    // =========================================================================
    // Keutamaan Flag
    // =========================================================================

    /**
     * Resolve priority label after all hard filters pass.
     *
     * Only applies when scholarship has 2+ income categories (preference).
     * All other scholarships default to 'priority_match'.
     *
     * Returns:
     *   'priority_match' → student in preferred group OR no preference
     *   'general_match'  → student outside preferred group (still eligible)
     */
    private function resolveKeutamaan($student, $criteria): string
    {
        $categories = $criteria->income_categories ?? [];

        if ($this->resolveIncomeType($categories) !== 'preference') {
            return 'priority_match';
        }

        $preferred       = array_map('strtoupper', $categories);
        $studentCategory = strtoupper($student->income_category ?? '');

        return in_array($studentCategory, $preferred, true)
            ? 'priority_match'
            : 'general_match';
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Auto-resolve income type from number of ticked categories.
     *
     * 0 ticked  → null          no restriction
     * 1 ticked  → 'requirement' hard filter (syarat wajib)
     * 2+ ticked → 'preference'  keutamaan flag (not a filter)
     */
    private function resolveIncomeType(array $categories): ?string
    {
        $count = count($categories);

        if ($count === 0) return null;
        if ($count === 1) return 'requirement';
        return 'preference';
    }
}