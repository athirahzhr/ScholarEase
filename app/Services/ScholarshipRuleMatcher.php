<?php

namespace App\Services;

/**
 * Rule-Based Scholarship Matcher
 * ================================
 * Designed for FYP — explainable rule-based recommendation system.
 *
 * HOW IT WORKS (for examiner explanation):
 * -----------------------------------------
 * STEP 1 — Hard Filters (Pass/Fail)
 *   Citizenship, Bumiputera, Study Level, Field of Study
 *   → If student fails ANY of these, scholarship is excluded entirely.
 *   → These are binary because they are fixed policy requirements with no "close enough".
 *
 * STEP 2 — Scored Criteria (Range-based, has partial credit)
 *   SPM Result, Monthly Income, Age Gap
 *   → These have a natural range/spectrum, so we reward closeness.
 *   → e.g. A student 1 A short of requirement is closer than one 5 As short.
 *
 * STEP 3 — Bonus Points
 *   Leadership, Bumiputera Priority
 *   → Optional traits preferred by the scholarship provider.
 *   → Adds small score boost but never disqualifies.
 *
 * SCORING BUDGET (total = 100 pts):
 *   SPM Result       → 40 pts  (highest weight, most academic relevance)
 *   Monthly Income   → 35 pts  (second, financial need is key for most scholarships)
 *   Age              → 25 pts  (tertiary, most students fall within range)
 *   ─────────────────────────
 *   Base Total       = 100 pts
 *   Bonus (each)     = +5 pts  (can push past 100, capped at 100)
 *
 * RECOMMENDATION THRESHOLD: Score >= 50% after hard filter pass.
 */
class ScholarshipRuleMatcher
{
    // ── Scoring weights (must total 100) ─────────────────────────────────────
    private const W_SPM    = 40;
    private const W_INCOME = 35;
    private const W_AGE    = 25;

    // ── Bonus (on top, capped at 100 final) ──────────────────────────────────
    private const W_BONUS = 5;

    // ── Minimum score to appear in recommendations ────────────────────────────
    private const MIN_SCORE = 50;

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
        // All must pass. If any fail → ineligible, stop here.
        $hardFilters = $this->runHardFilters($student, $criteria);

        $failedAny = in_array(false, $hardFilters, true);

        if ($failedAny) {
            return [
                'eligible'    => false,
                'score'       => 0,
                'breakdown'   => $hardFilters,
                'match_level' => 'Not Eligible',
            ];
        }

        // ── STEP 2: Scored Criteria (range-based) ────────────────────────────
        $score    = 0;
        $maxScore = 0;
        $scoreBreakdown = [];

        // SPM
        [$earned, $max, $detail] = $this->scoreSpm($student, $criteria);
        $score              += $earned;
        $maxScore           += $max;
        $scoreBreakdown['spm'] = [
            'earned' => $earned,
            'max'    => $max,
            'detail' => $detail,
        ];

        // Monthly Income
        [$earned, $max, $detail] = $this->scoreIncome($student, $criteria);
        $score                 += $earned;
        $maxScore              += $max;
        $scoreBreakdown['income'] = [
            'earned' => $earned,
            'max'    => $max,
            'detail' => $detail,
        ];

        // Age
        [$earned, $max, $detail] = $this->scoreAge($student, $criteria);
        $score              += $earned;
        $maxScore           += $max;
        $scoreBreakdown['age'] = [
            'earned' => $earned,
            'max'    => $max,
            'detail' => $detail,
        ];

        // ── STEP 3: Bonus Points ─────────────────────────────────────────────
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
    // STEP 1 — Hard Filters (Pass/Fail only, no scoring)
    // =========================================================================

    private function runHardFilters($student, $criteria): array
    {
        return [
            // Must be correct citizenship
            'citizenship' => $this->checkCitizenship($student, $criteria),

            // Must be Bumiputera if required
            'bumiputera'  => $this->checkBumiputera($student, $criteria),

            // Must be in allowed study level (e.g. Diploma, Degree)
            'study_level' => $this->checkStudyLevel($student, $criteria),

            // Must be in allowed field of study (e.g. Engineering, Medicine)
            'field'       => $this->checkField($student, $criteria),
        ];
    }

    private function checkCitizenship($student, $criteria): bool
    {
        // No requirement set = open to all
        if (!$criteria->citizenship_required) {
            return true;
        }

        return strcasecmp($student->citizenship, $criteria->citizenship_required) === 0;
    }

    private function checkBumiputera($student, $criteria): bool
    {
        // No requirement = open to all races
        if (!$criteria->bumiputera_required) {
            return true;
        }

        return (bool) $student->bumiputera;
    }

    private function checkStudyLevel($student, $criteria): bool
    {
        $allowed = $criteria->study_paths ?? [];

        // No restriction = all levels accepted
        return empty($allowed) || in_array($student->study_level, $allowed, true);
    }

    private function checkField($student, $criteria): bool
    {
        $allowed = $criteria->fields_of_study ?? [];

        // No restriction = all fields accepted
        return empty($allowed) || in_array($student->field_of_study, $allowed, true);
    }

    // =========================================================================
    // STEP 2 — Scored Criteria (range-based partial credit)
    // Each method returns: [points_earned, max_points, detail_string]
    // =========================================================================

    /**
     * SPM Result — 40 pts
     *
     * Rule:
     *   Meets or exceeds requirement   → 40/40 (full)
     *   1 A short                      → 28/40 (70%)
     *   2 As short                     → 18/40 (45%)
     *   3+ As short                    → 6/40  (15% floor — still passes filter)
     *
     * Why partial credit?
     *   A student with 9As applying for a 10A scholarship is still highly
     *   competitive vs one with 5As. Partial credit preserves this ranking.
     */
    private function scoreSpm($student, $criteria): array
    {
        $max = self::W_SPM;

        // No SPM requirement set → full marks
        if ($criteria->min_spm_as === null) {
            return [$max, $max, 'No SPM requirement — full marks awarded'];
        }

        $shortfall = $criteria->min_spm_as - $student->total_as;

        if ($shortfall <= 0) {
            return [$max, $max, "Meets requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)"];
        }

        $tiers = [
            1 => [0.70, "1A short of requirement"],
            2 => [0.45, "2As short of requirement"],
        ];

        [$multiplier, $label] = $tiers[$shortfall] ?? [0.15, "{$shortfall}As short of requirement"];

        $earned = (int) round($max * $multiplier);

        return [$earned, $max, $label];
    }

    /**
     * Monthly Income — 35 pts
     *
     * Rule:
     *   Within limit                   → 35/35 (full)
     *   Exceeds by ≤ RM500             → 26/35 (75%)  ← tighter tiers than before
     *   Exceeds by RM501–RM1,500       → 18/35 (50%)
     *   Exceeds by RM1,501–RM3,000     → 11/35 (30%)
     *   Exceeds by > RM3,000           → 4/35  (10% floor)
     *
     * Why partial credit?
     *   Income limits are policy thresholds. A family RM200 over the limit
     *   should rank higher than one RM5,000 over.
     */
    private function scoreIncome($student, $criteria): array
    {
        $max = self::W_INCOME;

        // No income limit set → full marks
        if ($criteria->max_monthly_income === null) {
            return [$max, $max, 'No income requirement — full marks awarded'];
        }

        $income = $student->monthly_income;
        $limit  = $criteria->max_monthly_income;

        if ($income <= $limit) {
            return [$max, $max, "Within limit (RM{$income} ≤ RM{$limit})"];
        }

        $excess = $income - $limit;

        $tiers = [
            500  => [0.75, "Exceeds limit by RM{$excess} (≤ RM500)"],
            1500 => [0.50, "Exceeds limit by RM{$excess} (RM501–RM1,500)"],
            3000 => [0.30, "Exceeds limit by RM{$excess} (RM1,501–RM3,000)"],
        ];

        foreach ($tiers as $threshold => [$multiplier, $label]) {
            if ($excess <= $threshold) {
                return [(int) round($max * $multiplier), $max, $label];
            }
        }

        return [(int) round($max * 0.10), $max, "Exceeds limit by RM{$excess} (> RM3,000)"];
    }

    /**
     * Age — 25 pts
     *
     * Rule:
     *   Within age range               → 25/25 (full)
     *   1 year outside range           → 13/25 (50%)
     *   2+ years outside range         → 0/25  (0 — too far off)
     *
     * Why partial credit and NOT a hard filter?
     *   Age limits for scholarships are often soft guidelines, not strict
     *   legal requirements. A student 1 year over may still be considered.
     *   If your scholarships treat age as strict, move this to hard filters.
     */
    private function scoreAge($student, $criteria): array
    {
        $max = self::W_AGE;

        $hasMin = $criteria->min_age !== null;
        $hasMax = $criteria->max_age !== null;

        // No age requirement → full marks
        if (!$hasMin && !$hasMax) {
            return [$max, $max, 'No age requirement — full marks awarded'];
        }

        $age = $student->age;
        $gap = 0;

        if ($hasMin && $age < $criteria->min_age) {
            $gap = $criteria->min_age - $age;
        } elseif ($hasMax && $age > $criteria->max_age) {
            $gap = $age - $criteria->max_age;
        }

        if ($gap === 0) {
            $range = ($hasMin ? $criteria->min_age : '?') . '–' . ($hasMax ? $criteria->max_age : '?');
            return [$max, $max, "Within age range ({$age} yrs, range {$range})"];
        }

        if ($gap === 1) {
            return [
                (int) round($max * 0.50),
                $max,
                "1 year outside age range (age {$age})",
            ];
        }

        return [0, $max, "{$gap} years outside age range (age {$age}) — no score"];
    }

    // =========================================================================
    // STEP 3 — Bonus Points
    // =========================================================================

    /**
     * Bonus — up to +5 pts each (capped at 100 overall)
     *
     * Bonuses reward preferred but non-mandatory traits.
     * They can tip the ranking between two equally-scoring candidates.
     */
    private function scoreBonus($student, $criteria): array
    {
        $earned  = 0;
        $details = [];

        if ($criteria->leadership_priority && $student->has_leadership) {
            $earned            += self::W_BONUS;
            $details[]          = 'Leadership bonus (+' . self::W_BONUS . ')';
        }

        if ($criteria->bumiputera_priority && $student->bumiputera) {
            $earned            += self::W_BONUS;
            $details[]          = 'Bumiputera priority bonus (+' . self::W_BONUS . ')';
        }

        return [$earned, $details];
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function getMatchLevel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'High Match',
            $score >= 60 => 'Medium Match',
            default      => 'Low Match',
        };
    }
}