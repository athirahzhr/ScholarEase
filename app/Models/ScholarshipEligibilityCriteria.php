<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipEligibilityCriteria extends Model
{
    use HasFactory;

    protected $table = 'scholarship_eligibility_criteria';

    protected $fillable = [
        'scholarship_id',

        // Academic
        'min_spm_as',
        'max_spm_as',
        'required_subjects',

        // Financial
        'max_monthly_income',
        'income_categories',

        // Study
        'study_paths',
        'fields_of_study',
        'study_destination',

        // Demographic
        'citizenship_required',
        'state_requirement',

        // Age
        'min_age',
        'max_age',

        // Bumiputera
        'bumiputera_required',
        'bumiputera_priority',

        // Leadership
        'leadership_required',
        'leadership_priority',

        // Scoring
        'priority_weight',
        'max_score',

        // Extra
        'notes',
    ];

    protected $casts = [

        // Arrays / JSON
        'required_subjects' => 'array',
        'study_paths' => 'array',
        'fields_of_study' => 'array',
        'income_categories' => 'array',

        // Boolean
        'bumiputera_required' => 'boolean',
        'bumiputera_priority' => 'boolean',

        'leadership_required' => 'boolean',
        'leadership_priority' => 'boolean',

        // Numeric
        'max_monthly_income' => 'decimal:2',

        'priority_weight' => 'integer',
        'max_score' => 'integer',
    ];

    /**
     * Scholarship relationship
     */
    public function scholarship()
    {
        return $this->belongsTo(
            Scholarship::class
        );
    }
}