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
 *   Citizenship, Bumiputera, Study Level, Field of Study, Age
 *   → Student must pass ALL. Any failure = excluded immediately.
 *   → Binary because these are fixed policy requirements.
 *
 * STEP 2 — Scored Criteria (Range-based, partial credit)
 *   SPM Result (50 pts) + Monthly Income (50 pts) = 100 pts base
 *   → These have a natural spectrum — rewards closeness to requirement.
 *
 * STEP 3 — Bonus Points (on top, capped at 100 final)
 *   Leadership (+5) and Bumiputera Priority (+5)
 *   → Preferred but not mandatory traits.
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
        $score    = 0;
        $maxScore = 0;
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

        // Monthly Income
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
            'study_level' => $this->checkStudyLevel($student, $criteria),
            'field'       => $this->checkField($student, $criteria),
            'age'         => $this->checkAge($student, $criteria),
        ];
    }

    private function checkCitizenship($student, $criteria): bool
    {
        // No requirement = open to all
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

        // Empty = no restriction = pass
        return empty($allowed) || in_array($student->study_level, $allowed, true);
    }

    private function checkField($student, $criteria): bool
    {
        $allowed = $criteria->fields_of_study ?? [];

        // Empty = open to all fields = pass
        return empty($allowed) || in_array($student->field_of_study, $allowed, true);
    }

    private function checkAge($student, $criteria): bool
    {
        // Below minimum age
        if ($criteria->min_age !== null && $student->age < $criteria->min_age) {
            return false;
        }

        // Above maximum age
        if ($criteria->max_age !== null && $student->age > $criteria->max_age) {
            return false;
        }

        return true;
    }

    // =========================================================================
    // STEP 2 — Scored Criteria (range-based, partial credit)
    // Each returns: [points_earned, max_points, detail_string]
    // =========================================================================

    /**
     * SPM Result — 50 pts
     *
     * Scoring Tiers:
     *   Meets or exceeds requirement  → 50/50 (100%)
     *   1 A short                     → 35/50  (70%)
     *   2 As short                    → 23/50  (45%)
     *   3+ As short                   →  8/50  (15% floor)
     *
     * Rationale:
     *   SPM result exists on a spectrum. A student with 9As applying for
     *   a 10A scholarship is still highly academic. Partial credit
     *   preserves meaningful ranking between close candidates.
     */
    private function scoreSpm($student, $criteria): array
    {
        $max = self::W_SPM;

        // No SPM requirement → full marks
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
     * Monthly Income — 50 pts
     *
     * Scoring Tiers:
     *   Within limit                  → 50/50 (100%)
     *   Exceeds by ≤ RM500            → 38/50  (75%)
     *   Exceeds by RM501–RM1,500      → 25/50  (50%)
     *   Exceeds by RM1,501–RM3,000    → 15/50  (30%)
     *   Exceeds by > RM3,000          →  5/50  (10% floor)
     *
     * Rationale:
     *   Income limits are policy thresholds but a family RM200 over the
     *   limit is far closer in financial need than one RM5,000 over.
     *   Partial credit reflects real proximity to financial eligibility.
     */
    private function scoreIncome($student, $criteria): array
    {
        $max = self::W_INCOME;

        // No income limit → full marks
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
            500  => [0.75, "Exceeds by RM{$excess} (≤ RM500 over limit)"],
            1500 => [0.50, "Exceeds by RM{$excess} (RM501–RM1,500 over limit)"],
            3000 => [0.30, "Exceeds by RM{$excess} (RM1,501–RM3,000 over limit)"],
        ];

        foreach ($tiers as $threshold => [$multiplier, $label]) {
            if ($excess <= $threshold) {
                return [(int) round($max * $multiplier), $max, $label];
            }
        }

        return [(int) round($max * 0.10), $max, "Exceeds by RM{$excess} (> RM3,000 over limit)"];
    }

    // =========================================================================
    // STEP 3 — Bonus Points
    // =========================================================================

    /**
     * Bonus — up to +5 pts each (final score capped at 100)
     *
     * Rationale:
     *   Bonuses reward preferred but non-mandatory traits.
     *   They break ties between equally-scored candidates.
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
    // Helper
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