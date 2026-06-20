<?php

namespace App\Services;

class ScholarshipRuleMatcher
{
    /**
     * Get scholarship recommendations
     */
    public function getRecommendations(
        $student,
        $scholarships
    ) {
        $results = [];

        foreach ($scholarships as $scholarship) {

            $criteria = $scholarship->eligibilityCriteria;

            if (!$criteria) {
                continue;
            }

            $result = $this->matchScholarship(
                $student,
                $criteria
            );

            // ELIGIBLE + MINIMUM SCORE 50%
            if (
                $result['score'] >= 50
            ) {

                $scholarship->match_score =
                    $result['score'];

                $scholarship->match_breakdown =
                    $result['breakdown'];

                $scholarship->match_level =
                    $result['match_level'];

                $results[] = $scholarship;
            }
        }

        return collect($results)
            ->sortByDesc('match_score')
            ->values();
    }

    /**
     * Match student with scholarship rules
     */
    public function matchScholarship(
        $student,
        $criteria
    ) {

        $score = 0;
        $maxScore = 0;
        

        $breakdown = [];

        // =========================
        // SPM RESULT
        // =========================
        $breakdown['spm'] = false;

        $maxScore += 25;

        if ($criteria->min_spm_as !== null) {

            $difference =
                $criteria->min_spm_as -
                $student->total_as;

            if ($difference <= 0) {

                $score += 25;
                $breakdown['spm'] = true;

            } elseif ($difference == 1) {

                $score += 18;

            } elseif ($difference == 2) {

                $score += 12;

            } else {

                $score += 5;
            }

        } else {

            $score += 25;
            $breakdown['spm'] = true;
        }

        // =========================
        // MONTHLY INCOME
        // =========================
       $breakdown['income'] = false;

        $maxScore += 20;

        if ($criteria->max_monthly_income !== null) {

            if (
                $student->monthly_income <=
                $criteria->max_monthly_income
            ) {

                $score += 20;
                $breakdown['income'] = true;

            } else {

                $excess =
                    $student->monthly_income -
                    $criteria->max_monthly_income;

                if ($excess <= 1000) {

                    $score += 15;

                } elseif ($excess <= 3000) {

                    $score += 10;

                } else {

                    $score += 5;
                }
            }

        } else {

            $score += 20;
            $breakdown['income'] = true;
        }

        // =========================
        // STUDY LEVEL
        // =========================
        $breakdown['study_level'] = false;


        $studyLevels =
            $criteria->study_paths ?? [];

        $maxScore += 15;

        if (
            !empty($studyLevels) &&
            !in_array(
                $student->study_level,
                $studyLevels
            )
        ) {

            $score += 5;

        } else {

            $score += 15;
            $breakdown['study_level'] = true;
        }

        // =========================
        // FIELD OF STUDY
        // =========================
        $breakdown['field'] = false;

        $fields =
            $criteria->fields_of_study ?? [];

        $maxScore += 15;

        if (!empty($fields)) {

            if (
                in_array(
                    $student->field_of_study,
                    $fields
                )
            ) {

                $score += 15;
                $breakdown['field'] = true;

            } else {

                $score += 8;
            }

        } else {

            $score += 15;
            $breakdown['field'] = true;
        }

        // =========================
        // AGE
        // =========================
        $breakdown['age'] = false;

        $maxScore += 10;

        if (
            ($criteria->min_age &&
                $student->age < $criteria->min_age)
            ||
            ($criteria->max_age &&
                $student->age > $criteria->max_age)
        ) {

            $score += 3;

        } else {

            $score += 10;
            $breakdown['age'] = true;
        }


        // =========================
        // BUMIPUTERA
        // =========================
        $breakdown['bumiputera'] = false;

        $maxScore += 5;

        if (
    $criteria->bumiputera_required &&
    !$student->bumiputera
        ) {

            $score += 2;

        } else {

            $score += 5;
            $breakdown['bumiputera'] = true;
        }

        // =========================
        // CITIZENSHIP
        // =========================
        $breakdown['citizenship'] = false;

        $maxScore += 5;

        if (
            $criteria->citizenship_required &&
            strtolower($student->citizenship)
            !== strtolower(
                $criteria->citizenship_required
            )
        ) {

            $score += 2;

        } else {

            $score += 5;
            $breakdown['citizenship'] = true;
        }

        // =========================
        // PRIORITY BONUS
        // =========================
        $weight =
            $criteria->priority_weight ?? 1;


        // Leadership priority
        if (
            $criteria->leadership_priority &&
            $student->has_leadership
        ) {
            $score += 5 * $weight;
        }

        // Bumiputera priority
        if (
            $criteria->bumiputera_priority &&
            $student->bumiputera
        ) {
            $score += 5 * $weight;
        }

        // LIMIT 100
        $percentage = $maxScore > 0
        ? round(($score / $maxScore) * 100)
        : 0;

        $percentage = min($percentage, 100);

        return [
            'score' => $percentage,
            'breakdown' => $breakdown,
            'match_level' => match (true) {
                $percentage >= 80 => 'High Match',
                $percentage >= 60 => 'Medium Match',
                default => 'Low Match',
            },
        ];
    }
}