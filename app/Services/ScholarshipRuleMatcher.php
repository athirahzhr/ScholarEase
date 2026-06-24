<?php

namespace App\Services;

/**
 * Rule-Based Scholarship Matcher
 * ================================
 * FYP — Explainable Rule-Based Recommendation System
 *
 * HOW IT WORKS:
 * ─────────────────────────────────────────────────────
 * STEP 1 — Hard Filters (Pass/Fail)
 *   Citizenship, Bumiputera, Study Level, Field of Study, Age,
 *   SPM (near-hard filter, shortfall > 2 = excluded),
 *   Income (ceiling OR single-category requirement)
 *   → Student must pass ALL. Any failure = excluded immediately.
 *
 * STEP 2 — Scored Criteria (Range-based, partial credit)
 *   SPM Result (50 pts) + Income (50 pts) = 100 pts base
 *   → SPM: partial credit for shortfall 0, 1, or 2.
 *   → Income: scored ONLY when 2+ categories ticked (keutamaan).
 *
 * STEP 3 — Bonus Points (on top, capped at 100 final)
 *   Leadership (+5) and Bumiputera Priority (+5)
 *
 * ─────────────────────────────────────────────────────
 * INCOME TYPE — auto-resolved from income_categories count:
 *
 *   0 ticked  → null        No restriction. Full marks.
 *               "Terbuka kepada semua"
 *
 *   1 ticked  → requirement Hard filter. Only that category eligible.
 *               "B40 sahaja" — M40/T20 students excluded ❌
 *
 *   2+ ticked → preference  Scored criteria (keutamaan).
 *               "Keutamaan B40 dan M40" — T20 gets 25/50 ✅
 *
 * No separate income_type column needed in database.
 * System auto-derives type from count of ticked categories.
 *
 * ─────────────────────────────────────────────────────
 * STUDENT INCOME DATA:
 *   User inputs monthly_income (actual RM e.g. RM4,500).
 *   System auto-derives income_category (B40/M40/T20) on profile save.
 *
 *   monthly_income  → used for max_monthly_income ceiling check
 *   income_category → used for category requirement/preference check
 *
 * INCOME THRESHOLDS (Rafizi Ramli, Ministry of Economy Malaysia, 2023):
 *   B40 → ≤ RM4,850/month
 *   M40 → RM4,851 – RM10,960/month
 *   T20 → > RM10,960/month
 *
 * RECOMMENDATION THRESHOLD: Score >= 50
 * ─────────────────────────────────────────────────────
 */
class ScholarshipRuleMatcher
{
    // ── Scoring weights (must total 100) ─────────────────────────────────────
    private const W_SPM    = 50;
    private const W_INCOME = 50;

    // ── Bonus (on top, final score capped at 100) ─────────────────────────────
    private const W_BONUS = 5;

    // ── Minimum score to appear in recommendations ────────────────────────────
    private const MIN_SCORE = 50;

    // ── SPM near-hard filter tolerance ───────────────────────────────────────
    // Shortfall > this value = excluded (too far from requirement)
    private const SPM_MAX_SHORTFALL = 2;

    /**
     * Official Malaysian household income thresholds.
     * Source: Ministry of Economy Malaysia (Rafizi Ramli, 2023)
     *
     * B40 → ≤ RM4,850/month
     * M40 → RM4,851 – RM10,960/month
     * T20 → > RM10,960/month
     */
    private const INCOME_THRESHOLDS = [
        'B40' => 4850,
        'M40' => 10960,
    ];

    // =========================================================================
    // PUBLIC: Auto-derive income category from actual monthly income (RM)
    // Call this when saving student profile, store result in income_category.
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
        $results = [];

        foreach ($scholarships as $scholarship) {
            $criteria = $scholarship->eligibilityCriteria;

            if (!$criteria) {
                continue;
            }

            $result = $this->matchScholarship($student, $criteria);

            if ($result['eligible'] && $result['score'] >= self::MIN_SCORE) {
                $scholarship->match_score     = $result['score'];
                $scholarship->match_breakdown = $result['breakdown'];
                $scholarship->match_level     = $result['match_level'];
                $results[] = $scholarship;
            }
        }

        return collect($results)
            ->sortByDesc('match_score')
            ->values();
    }

    // =========================================================================
    // PUBLIC: Match Single Scholarship
    // =========================================================================

    public function matchScholarship($student, $criteria): array
    {
        // ── STEP 1: Hard Filters ─────────────────────────────────────────────
        $hardFilters = $this->runHardFilters($student, $criteria);
        $failedAny   = in_array(false, $hardFilters, true);

        if ($failedAny) {
            return [
                'eligible'    => false,
                'score'       => 0,
                'breakdown'   => $hardFilters,
                'match_level' => 'Not Eligible',
            ];
        }

        // ── STEP 2: Scored Criteria ───────────────────────────────────────────
        $score          = 0;
        $maxScore       = 0;
        $scoreBreakdown = [];

        // SPM Result
        [$earned, $max, $detail] = $this->scoreSpm($student, $criteria);
        $score    += $earned;
        $maxScore += $max;
        $scoreBreakdown['spm'] = [
            'earned' => $earned,
            'max'    => $max,
            'detail' => $detail,
        ];

        // Income (preference scoring — only when 2+ categories ticked)
        [$earned, $max, $detail] = $this->scoreIncome($student, $criteria);
        $score    += $earned;
        $maxScore += $max;
        $scoreBreakdown['income'] = [
            'earned' => $earned,
            'max'    => $max,
            'detail' => $detail,
        ];

        // ── STEP 3: Bonus Points ──────────────────────────────────────────────
        [$bonusEarned, $bonusDetail] = $this->scoreBonus($student, $criteria);
        $score += $bonusEarned;
        $scoreBreakdown['bonus'] = [
            'earned' => $bonusEarned,
            'detail' => $bonusDetail,
        ];

        // ── Final Score (capped at 100) ───────────────────────────────────────
        $percentage = $maxScore > 0
            ? (int) min(round(($score / $maxScore) * 100), 100)
            : 0;

        return [
            'eligible'    => true,
            'score'       => $percentage,
            'breakdown'   => array_merge($hardFilters, $scoreBreakdown),
            'match_level' => $this->getMatchLevel($percentage),
        ];
    }

    // =========================================================================
    // STEP 1 — Hard Filters (Pass/Fail, no scoring)
    // =========================================================================

    private function runHardFilters($student, $criteria): array
    {
        return [
            'citizenship' => $this->checkCitizenship($student, $criteria),
            'bumiputera'  => $this->checkBumiputera($student, $criteria),
            'state'       => $this->checkState($student, $criteria),
            'study_level' => $this->checkStudyLevel($student, $criteria),
            'field'       => $this->checkField($student, $criteria),
            'age'         => $this->checkAge($student, $criteria),
            'spm'         => $this->checkSpm($student, $criteria),
            'income'      => $this->checkIncome($student, $criteria),
        ];
    }

    private function checkCitizenship($student, $criteria): bool
    {
        if (!$criteria->citizenship_required) {
            return true;
        }

        return strcasecmp($student->citizenship, $criteria->citizenship_required) === 0;
    }

    private function checkState($student, $criteria): bool
    {
        if (!$criteria->state_requirement) {
            return true;
        }

        return strcasecmp(
            trim($student->state),
            trim($criteria->state_requirement)
        ) === 0;
    }

    private function checkBumiputera($student, $criteria): bool
    {
        if (!$criteria->bumiputera_required) {
            return true;
        }

        return (bool) $student->bumiputera;
    }

    private function checkStudyLevel($student, $criteria): bool
    {
        $allowed = $criteria->study_paths ?? [];

        return empty($allowed) || in_array($student->study_level, $allowed, true);
    }

    private function checkField($student, $criteria): bool
    {
        $allowed = $criteria->fields_of_study ?? [];

        return empty($allowed) || in_array($student->field_of_study, $allowed, true);
    }

    private function checkAge($student, $criteria): bool
    {
        if ($criteria->min_age !== null && $student->age < $criteria->min_age) {
            return false;
        }

        if ($criteria->max_age !== null && $student->age > $criteria->max_age) {
            return false;
        }

        return true;
    }

    /**
     * SPM Hard Filter (near-hard filter with tolerance of 2).
     *
     * Shortfall > SPM_MAX_SHORTFALL (2) → excluded entirely.
     * Students within tolerance proceed to Step 2 for partial scoring.
     */
    private function checkSpm($student, $criteria): bool
    {
        if ($criteria->min_spm_as === null) {
            return true;
        }

        $shortfall = $criteria->min_spm_as - $student->total_as;

        return $shortfall <= self::SPM_MAX_SHORTFALL;
    }

    /**
     * Income Hard Filter.
     *
     * Two separate checks, evaluated in this order:
     *
     * 1. max_monthly_income (RM ceiling)
     *    If set → compare student's actual monthly_income against it.
     *    "Pendapatan tidak melebihi RM5,000"
     *    RM8,000 vs RM5,000 → FAIL ❌
     *
     * 2. income_categories (category-based)
     *    Behaviour auto-resolved from count:
     *
     *    0 ticked → null        No restriction → pass ✅
     *    1 ticked → requirement Student category MUST match → hard filter
     *                           "B40 sahaja" — T20 → FAIL ❌
     *    2+ ticked → preference Not a hard filter → pass here ✅
     *                           Handled in Step 2 scoreIncome() as scored criteria.
     */
    private function checkIncome($student, $criteria): bool
    {
        // ── Check 1: RM ceiling ───────────────────────────────────────────────
        if ($criteria->max_monthly_income !== null) {
            return $student->monthly_income <= $criteria->max_monthly_income;
        }

        // ── Check 2: Category-based ───────────────────────────────────────────
        $incomeType = $this->resolveIncomeType($criteria);

        if ($incomeType === 'requirement') {
            $required        = array_map('strtoupper', $criteria->income_categories);
            $studentCategory = strtoupper($student->income_category ?? '');

            return in_array($studentCategory, $required, true);
        }

        // null or preference → pass here
        return true;
    }

    // =========================================================================
    // STEP 2 — Scored Criteria (range-based, partial credit)
    // Each returns: [points_earned, max_points, detail_string]
    // =========================================================================

    /**
     * SPM Result — 50 pts
     *
     * Only students within SPM_MAX_SHORTFALL (2) reach here.
     * checkSpm() already excluded students with shortfall > 2.
     *
     * Scoring Tiers:
     *   Meets or exceeds requirement  → 50/50 (100%)
     *   1 A short                     → 35/50  (70%)
     *   2 As short                    → 23/50  (45%)
     */
    private function scoreSpm($student, $criteria): array
    {
        $max = self::W_SPM;

        if ($criteria->min_spm_as === null) {
            return [$max, $max, 'No SPM requirement — full marks awarded'];
        }

        $shortfall = $criteria->min_spm_as - $student->total_as;

        if ($shortfall <= 0) {
            return [
                $max,
                $max,
                "Meets requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)",
            ];
        }

        $tiers = [
            1 => [0.70, "1A short of requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)"],
            2 => [0.45, "2As short of requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)"],
        ];

        [$multiplier, $label] = $tiers[$shortfall]
            ?? [0.15, "{$shortfall}As short of requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)"];

        return [(int) round($max * $multiplier), $max, $label];
    }

    /**
     * Income — 50 pts (preference scoring only)
     *
     * Only runs when 2+ categories are ticked (keutamaan).
     * Ceiling and single-category requirement are handled in Step 1.
     *
     * Uses student's AUTO-DERIVED income_category from monthly_income.
     *
     * Scoring:
     *   Student category in preferred list  → 50/50 (keutamaan — perfect fit)
     *   Student category NOT in list        → 25/50 (less preferred, not excluded)
     *   0 or 1 category ticked             → 50/50 (not a preference)
     */
    private function scoreIncome($student, $criteria): array
    {
        $max        = self::W_INCOME;
        $incomeType = $this->resolveIncomeType($criteria);

        // Only score when preference (2+ categories ticked)
        if ($incomeType !== 'preference') {
            return [$max, $max, 'No income preference — full marks awarded'];
        }

        $preferred       = array_map('strtoupper', $criteria->income_categories);
        $studentCategory = strtoupper($student->income_category ?? '');

        if (in_array($studentCategory, $preferred, true)) {
            return [
                $max,
                $max,
                "Preferred category match ({$studentCategory} — keutamaan diberikan)",
            ];
        }

        return [
            (int) round($max * 0.50),
            $max,
            "Outside preferred categories (student: {$studentCategory}, preferred: "
                . implode('/', $preferred) . ')',
        ];
    }

    // =========================================================================
    // STEP 3 — Bonus Points
    // =========================================================================

    /**
     * Bonus — up to +5 pts each (final score capped at 100)
     *
     * Bonuses reward preferred but non-mandatory traits.
     * They break ties between equally-scored candidates.
     */
    private function scoreBonus($student, $criteria): array
    {
        $earned  = 0;
        $details = [];

        if ($criteria->leadership_priority && $student->has_leadership) {
            $earned    += self::W_BONUS;
            $details[] = 'Leadership bonus (+' . self::W_BONUS . ' pts)';
        }

        if ($criteria->bumiputera_priority && $student->bumiputera) {
            $earned    += self::W_BONUS;
            $details[] = 'Bumiputera priority bonus (+' . self::W_BONUS . ' pts)';
        }

        return [$earned, $details];
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Auto-resolve income type from number of ticked categories.
     *
     * 0 ticked  → null          (no restriction)
     * 1 ticked  → 'requirement' (hard filter — syarat wajib)
     * 2+ ticked → 'preference'  (scored — keutamaan)
     *
     * No separate database column needed.
     * The count of income_categories determines the behaviour.
     */
    private function resolveIncomeType($criteria): ?string
    {
        $categories = $criteria->income_categories ?? [];

        if (empty($categories)) {
            return null;
        }

        if (count($categories) === 1) {
            return 'requirement';
        }

        return 'preference';
    }

    private function getMatchLevel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'High Match',
            $score >= 60 => 'Medium Match',
            default      => 'Low Match',
        };
    }
}