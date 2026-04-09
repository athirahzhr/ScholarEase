<?php

namespace App\Services;

class ScholarshipRuleMatcher
{
    /**
     * Confidence-based eligibility scoring
     */
    private function calculateConfidenceScore($student, $rule)
    {
        $total = 0;
        $matched = 0;

        // ===== Academic =====
        $total++;
        if ($rule->min_spm_as === null || $student->spm_as >= $rule->min_spm_as) {
            $matched++;
        }

        // ===== Income =====
        $total++;
        $incomeRules = $rule->income_categories
            ? json_decode($rule->income_categories, true)
            : null;

        if ($incomeRules === null || in_array($student->income_category, $incomeRules)) {
            $matched++;
        }

        // ===== Study Path =====
        $total++;
        $studyPaths = $rule->study_paths
            ? json_decode($rule->study_paths, true)
            : null;

        if ($studyPaths === null || in_array($student->study_path, $studyPaths)) {
            $matched++;
        }

        // ===== Citizenship =====
        $total++;
        if ($rule->citizenship_required === null ||
            $student->citizenship === $rule->citizenship_required) {
            $matched++;
        }

        // ===== Age =====
        $total++;
        if (
            ($rule->min_age === null || $student->age >= $rule->min_age) &&
            ($rule->max_age === null || $student->age <= $rule->max_age)
        ) {
            $matched++;
        }

        // ===== Base confidence =====
        if ($total === 0) {
            return 0;
        }

        $confidence = ($matched / $total) * $rule->max_score;

        // ===== Priority bonus (weighted) =====
        $weight = $rule->priority_weight ?? 1;

        if ($rule->leadership_priority && $student->has_leadership) {
            $confidence += 5 * $weight;
        }

        if ($rule->bumiputera_priority && $student->is_bumiputera) {
            $confidence += 5 * $weight;
        }

        return min(round($confidence), $rule->max_score);
    }
}
