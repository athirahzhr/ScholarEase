<?php

namespace App\Services;

use App\Models\Scholarship;
use App\Models\ScholarshipRule;

class ScholarshipInferenceEngine
{
    /**
     * Run inference for ALL scholarships (pipeline / cron)
     */
    public function run(): int
    {
        $count = 0;

        Scholarship::chunk(100, function ($scholarships) use (&$count) {
            foreach ($scholarships as $scholarship) {
                if ($this->inferSingle($scholarship)) {
                    $count++;
                }
            }
        });

        return $count;
    }

    /**
     * Run inference for ONE scholarship (observer / admin)
     */
    public function runSingle(Scholarship $scholarship): bool
    {
        return $this->inferSingle($scholarship);
    }

    /**
     * Core inference logic (shared)
     */
    private function inferSingle(Scholarship $scholarship): bool
    {
        // If nothing to infer, stop
        if (
            !$scholarship->academic_category &&
            !$scholarship->income_category &&
            !$scholarship->study_path
        ) {
            return false;
        }

        // 1️⃣ FULL RULE (A + B + C)
        $result = ScholarshipRule::where('rule_type', 'biasiswa_full')
            ->where('keyword', $this->buildKey(
                $scholarship->academic_category,
                $scholarship->income_category,
                $scholarship->study_path
            ))
            ->value('result');

        // 2️⃣ PARTIAL RULE (A + B / A + C / B + C)
        if (!$result) {
            $result = ScholarshipRule::where('rule_type', 'biasiswa_partial')
                ->whereIn('keyword', array_filter([
                    $this->buildKey($scholarship->academic_category, $scholarship->income_category),
                    $this->buildKey($scholarship->academic_category, null, $scholarship->study_path),
                    $this->buildKey(null, $scholarship->income_category, $scholarship->study_path),
                ]))
                ->value('result');
        }

        // 3️⃣ SINGLE RULE (A only / B only / C only)
        if (!$result) {
            $result = ScholarshipRule::where('rule_type', 'biasiswa_single')
                ->whereIn('keyword', array_filter([
                    $scholarship->academic_category,
                    $scholarship->income_category,
                    $scholarship->study_path,
                ]))
                ->value('result');
        }

        if ($result) {
            $scholarship->updateQuietly([
                'biasiswa_level' => $result,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Helper to generate rule keys
     * Examples:
     *  - A1|B1|C1
     *  - A1|B1
     *  - B1|C2
     */
    private function buildKey(?string $a = null, ?string $b = null, ?string $c = null): string
    {
        return collect([$a, $b, $c])
            ->filter()
            ->implode('|');
    }
}
