<?php

namespace App\Services;

/**
 * Rule-Based Scholarship Matcher
 * ================================
 *
 * ┌─────────────────────────────────────────────────────────────────┐
 * │                     SYSTEM DESIGN PHILOSOPHY                    │
 * │                                                                 │
 * │  Score = FIT, not merit.                                        │
 * │  A high score means "this scholarship was designed for you".    │
 * │  A low score means "you are not the target group".              │
 * │  Excluded means "you do not meet the policy requirement".       │
 * └─────────────────────────────────────────────────────────────────┘
 *
 * HOW IT WORKS:
 * ─────────────────────────────────────────────────────────────────
 *
 * STEP 1 — Hard Filters (Pass/Fail, no scoring)
 * ──────────────────────────────────────────────
 * These are SYARAT WAJIB — fixed policy requirements with no spectrum.
 * A student either qualifies or does not. There is no "almost eligible".
 * If ANY filter fails → student is excluded from this scholarship entirely.
 *
 *   Criteria              Reason it is a hard filter
 *   ─────────────────     ──────────────────────────────────────────────
 *   Citizenship           You are either Malaysian or you are not
 *   Bumiputera            Fixed racial policy requirement
 *   Study Level           Scholarship is for Degree only, or Diploma only
 *   Field of Study        Scholarship is for Engineering, not all fields
 *   Age                   Fixed age policy — 19 years is 19 years
 *   Income (RM ceiling)   "Tidak melebihi RM5,000" is a hard limit,
 *                         not a preference. Over the limit = not eligible.
 *
 * STEP 2 — Scored Criteria (Range-based, partial credit)
 * ───────────────────────────────────────────────────────
 * These criteria have a NATURAL SPECTRUM — there is meaningful distance
 * between candidates. Partial credit reflects how close the student is
 * to what the scholarship is specifically looking for.
 *
 *   SPM Result (50 pts)
 *   → A student 1A short is closer to the target than one 5As short.
 *   → Tolerance of 2As maximum. Beyond 2As → hard filter (excluded).
 *   → This prevents misleading recommendations for very weak matches.
 *
 *   Income Category — B40/M40/T20 (50 pts)
 *   → Only scored when scholarship uses "keutamaan" (preference) language.
 *   → "Keutamaan kepada B40/M40" ≠ "Syarat: pendapatan ≤ RM10,960"
 *   → Preferred category = full marks. Outside preference = partial marks.
 *   → T20 students are NOT excluded — scholarship does not exclude them,
 *     so the system must not impose a stricter rule than the policy itself.
 *
 * STEP 3 — Bonus Points (on top, capped at 100 final)
 * ─────────────────────────────────────────────────────
 *   Leadership (+5)        Preferred trait, not required
 *   Bumiputera priority    Provider prefers Bumiputera, but not mandatory
 *   → Breaks ties between equally-scored candidates.
 *
 * RECOMMENDATION THRESHOLD: Score >= 50
 * ─────────────────────────────────────────────────────────────────
 *
 * INCOME TREATMENT SUMMARY (key design decision):
 *
 *   Scholarship format          Treatment       Reason
 *   ─────────────────────────   ─────────────   ──────────────────────────
 *   max_monthly_income (RM)  →  Hard Filter  →  Syarat wajib, not flexible
 *   income_categories (B40…) →  Scored       →  Keutamaan = preference only
 *   Neither set              →  Pass + full  →  No income restriction
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

    /**
     * Official Malaysian household income thresholds.
     * Source: Ministry of Economy Malaysia (Rafizi Ramli, 2023)
     *
     * Used ONLY for PATH B (category preference scoring) —
     * converting a student's ringgit income into their B40/M40/T20 category
     * so it can be matched against the scholarship's preferred categories.
     *
     * These thresholds are NOT used as income ceilings for hard filtering.
     * Hard filtering uses max_monthly_income (PATH A) directly.
     *
     *   B40 → household income ≤ RM4,850/month       (bottom 40%)
     *   M40 → household income RM4,851–RM10,960/month (middle 40%)
     *   T20 → household income > RM10,960/month       (top 20%)
     */
    private const INCOME_CATEGORY_THRESHOLDS = [
        'B40' => 4850,
        'M40' => 10960,
    ];

    // ── SPM tolerance: max shortfall allowed before hard exclusion ────────────
    // Students more than this many As below the requirement are excluded.
    // Rationale: 1–2 As short is a close miss — still relevant to show.
    //            3+ As short is a fundamentally different academic profile
    //            and recommending them would be misleading to the student.
    private const SPM_MAX_SHORTFALL = 2;

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
        // All must pass. Any single failure → excluded immediately.
        // No scoring happens if student fails any hard filter.
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
        // Only reached if all hard filters passed.
        // Score reflects how well the student fits the scholarship's target.
        $score          = 0;
        $maxScore       = 0;
        $scoreBreakdown = [];

        // SPM Result (near-hard filter + scored)
        [$earned, $max, $detail] = $this->scoreSpm($student, $criteria);
        $score    += $earned;
        $maxScore += $max;
        $scoreBreakdown['spm'] = [
            'earned' => $earned,
            'max'    => $max,
            'detail' => $detail,
        ];

        // Income Category preference (only if scholarship uses B40/M40/T20)
        [$earned, $max, $detail] = $this->scoreIncomeCategory($student, $criteria);
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
            'citizenship'   => $this->checkCitizenship($student, $criteria),
            'bumiputera'    => $this->checkBumiputera($student, $criteria),
            'study_level'   => $this->checkStudyLevel($student, $criteria),
            'field'         => $this->checkField($student, $criteria),
            'age'           => $this->checkAge($student, $criteria),
            'income_limit'  => $this->checkIncomeLimit($student, $criteria),
        ];
    }

    /**
     * Citizenship — Hard Filter
     * You are either the required citizenship or you are not.
     * No partial state exists for nationality.
     */
    private function checkCitizenship($student, $criteria): bool
    {
        if (!$criteria->citizenship_required) {
            return true; // No restriction — open to all
        }

        return strcasecmp($student->citizenship, $criteria->citizenship_required) === 0;
    }

    /**
     * Bumiputera — Hard Filter
     * Fixed racial policy requirement.
     * Either the scholarship requires it or it does not.
     */
    private function checkBumiputera($student, $criteria): bool
    {
        if (!$criteria->bumiputera_required) {
            return true; // No restriction — open to all races
        }

        return (bool) $student->bumiputera;
    }

    /**
     * Study Level — Hard Filter
     * Scholarship is designed for a specific level (e.g. Degree only).
     * A Diploma student cannot be recommended for a Degree scholarship.
     */
    private function checkStudyLevel($student, $criteria): bool
    {
        $allowed = $criteria->study_paths ?? [];

        return empty($allowed) || in_array($student->study_level, $allowed, true);
    }

    /**
     * Field of Study — Hard Filter
     * Scholarship is for specific fields (e.g. Engineering, Medicine).
     * A student in an unlisted field is not the intended recipient.
     */
    private function checkField($student, $criteria): bool
    {
        $allowed = $criteria->fields_of_study ?? [];

        return empty($allowed) || in_array($student->field_of_study, $allowed, true);
    }

    /**
     * Age — Hard Filter
     * Fixed age policy. Age ranges are strict boundaries.
     * A student 1 year over the limit does not qualify — no partial state.
     */
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
     * Income Limit — Hard Filter (PATH A only)
     *
     * This filter ONLY activates when the scholarship sets max_monthly_income
     * as a direct ringgit ceiling (e.g. "Tidak melebihi RM5,000").
     *
     * Rationale:
     *   A ringgit ceiling is SYARAT WAJIB — a fixed policy requirement.
     *   "Pendapatan tidak melebihi RM5,000" means exactly that.
     *   A student earning RM6,000 does not meet this requirement.
     *   There is no "almost qualifying" for a stated income limit —
     *   the same way there is no "almost Malaysian" for citizenship.
     *
     *   Giving partial marks to over-limit students would be misleading:
     *   the system would recommend a scholarship the student
     *   is explicitly ineligible for.
     *
     * Note:
     *   income_categories (B40/M40/T20) are NOT handled here.
     *   They use "keutamaan" (preference) language, not a hard ceiling,
     *   and are therefore scored in STEP 2 instead.
     *
     * Returns true (pass) when:
     *   - No max_monthly_income is set (no restriction)
     *   - Student income is within the stated limit
     *
     * Returns false (fail/excluded) when:
     *   - max_monthly_income is set AND student income exceeds it
     */
    private function checkIncomeLimit($student, $criteria): bool
    {
        // No ringgit ceiling set → this filter does not apply → pass
        if ($criteria->max_monthly_income === null) {
            return true;
        }

        // Student must be within the stated ceiling
        return $student->monthly_income <= $criteria->max_monthly_income;
    }

    // =========================================================================
    // STEP 2 — Scored Criteria (range-based, partial credit)
    // Each returns: [points_earned, max_points, detail_string]
    // =========================================================================

    /**
     * SPM Result — 50 pts (near-hard filter + scored)
     *
     * SPM acts as BOTH a near-hard filter and a scored criterion:
     *
     *   Shortfall 0   → Meets requirement     → 50/50 (100%) — perfect fit
     *   Shortfall 1   → 1A short              → 35/50  (70%) — very close
     *   Shortfall 2   → 2As short             → 23/50  (45%) — borderline
     *   Shortfall 3+  → Too far from target   → EXCLUDED (hard filter kicks in)
     *
     * Why exclude at shortfall > 2?
     *   A student 3+ As below the requirement has a fundamentally different
     *   academic profile from the scholarship's target. Recommending them
     *   would be misleading — it gives false hope and wastes their time.
     *   The tolerance of 2As covers genuine close misses (e.g. 8As for a
     *   9A scholarship) without opening recommendations to clearly
     *   unsuitable candidates.
     *
     * Why partial credit within the tolerance?
     *   1A short is objectively closer to the target than 2As short.
     *   Partial credit preserves this meaningful ranking between
     *   close candidates — a 9A student and an 8A student competing
     *   for the same scholarship should not receive identical scores.
     *
     * Why not make SPM a pure hard filter (pass/fail only)?
     *   Because "1A short" carries real information about academic
     *   closeness. Discarding that information produces a less useful
     *   recommendation — two students with 8As and 4As would look
     *   identical to the system even though one is far more suitable.
     */
    private function scoreSpm($student, $criteria): array
    {
        $max = self::W_SPM;

        // No SPM requirement → scholarship open to all academic levels → full marks
        if ($criteria->min_spm_as === null) {
            return [$max, $max, 'No SPM requirement — full marks awarded'];
        }

        $shortfall = $criteria->min_spm_as - $student->total_as;

        // Meets or exceeds requirement → perfect academic fit
        if ($shortfall <= 0) {
            return [
                $max,
                $max,
                "Meets requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)",
            ];
        }

        // Beyond tolerance → should have been caught in hard filter
        // This is a safety fallback — return 0 and flag as not suitable
        if ($shortfall > self::SPM_MAX_SHORTFALL) {
            return [
                0,
                $max,
                "{$shortfall}As short — beyond tolerance (max allowed: " . self::SPM_MAX_SHORTFALL . "As short)",
            ];
        }

        // Within tolerance (1–2 As short) → partial credit
        $tiers = [
            1 => [0.70, "1A short of requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)"],
            2 => [0.45, "2As short of requirement ({$student->total_as}A / {$criteria->min_spm_as}A required)"],
        ];

        [$multiplier, $label] = $tiers[$shortfall];

        return [(int) round($max * $multiplier), $max, $label];
    }

    /**
     * Income Category — 50 pts (PATH B only — "keutamaan" scholarships)
     *
     * This scoring method ONLY runs when the scholarship uses income
     * categories (B40/M40/T20) with preference language like:
     *   "Keutamaan diberikan kepada calon daripada kategori B40 dan M40"
     *
     * It does NOT run for scholarships with max_monthly_income (ringgit
     * ceiling) — those are handled as a hard filter in STEP 1.
     *
     * Why score instead of filter for categories?
     *   Because "keutamaan" means preference, not requirement.
     *   The scholarship does not write "T20 students cannot apply."
     *   It writes "B40/M40 students are preferred."
     *   The system must not be stricter than the policy itself.
     *   Excluding T20 students would override the scholarship's own intent.
     *
     * Scoring logic:
     *   Student category IN preferred list     → 50/50 (100%)
     *     Perfect fit — student is exactly the target group.
     *
     *   Student category NOT in preferred list → 25/50  (50%)
     *     Less preferred but not excluded.
     *     Score of 50% communicates: "this scholarship exists for
     *     someone in a different financial situation, but you may still apply."
     *
     * Why 50% for non-preferred?
     *   0% would mean excluded — which contradicts "keutamaan" policy.
     *   100% would mean perfectly matched — which ignores the preference.
     *   50% is the midpoint: acknowledged but not the primary target.
     *
     * No income criteria at all → scholarship has no income restriction
     * → full marks (50/50) — all income groups are equally welcome.
     */
    private function scoreIncomeCategory($student, $criteria): array
    {
        $max = self::W_INCOME;

        // No income criteria of any kind → no restriction → full marks
        if (empty($criteria->income_categories)) {
            return [$max, $max, 'No income requirement — full marks awarded'];
        }

        // Convert student ringgit income to B40/M40/T20 category
        $studentCategory = $this->incomeToCategory($student->monthly_income);
        $preferred       = array_map('strtoupper', $criteria->income_categories);

        if (in_array($studentCategory, $preferred, true)) {
            return [
                $max,
                $max,
                "Preferred category match ({$studentCategory}) — keutamaan diberikan",
            ];
        }

        // Student is outside preferred categories but not excluded
        return [
            (int) round($max * 0.50),
            $max,
            "Outside preferred categories (student: {$studentCategory}, preferred: "
                . implode('/', $preferred)
                . ') — less preferred but eligible',
        ];
    }

    /**
     * Convert monthly income (ringgit) to B40/M40/T20 category.
     * Source: Ministry of Economy Malaysia (Rafizi Ramli, 2023)
     *
     * Used exclusively by scoreIncomeCategory() to determine
     * which group the student belongs to before matching
     * against the scholarship's preferred categories.
     */
    private function incomeToCategory(int $income): string
    {
        if ($income <= self::INCOME_CATEGORY_THRESHOLDS['B40']) {
            return 'B40';
        }

        if ($income <= self::INCOME_CATEGORY_THRESHOLDS['M40']) {
            return 'M40';
        }

        return 'T20';
    }

    // =========================================================================
    // STEP 3 — Bonus Points
    // =========================================================================

    /**
     * Bonus — up to +5 pts each (final score capped at 100)
     *
     * Bonuses reward traits that scholarship providers prefer
     * but do not mandate. They serve one purpose: breaking ties
     * between two candidates with identical base scores.
     *
     * A student with leadership experience is a better fit for a
     * scholarship that values leadership — the bonus reflects this
     * without penalising students who lack it.
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