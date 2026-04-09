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

        'min_spm_as',
        'max_spm_as',

        'academic_categories',
        'income_categories',
        'study_paths',
        'fields_of_study',

        'max_monthly_income',
        'gender_requirement',

        'min_age',
        'max_age',

        'bumiputera_required',
        'bumiputera_priority',

        'match_all_criteria',
        'priority_weight',
    ];

    protected $casts = [
        'academic_categories' => 'array',
        'income_categories'   => 'array',
        'study_paths'         => 'array',
        'fields_of_study'     => 'array',

        'bumiputera_required' => 'boolean',
        'bumiputera_priority' => 'boolean',
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
