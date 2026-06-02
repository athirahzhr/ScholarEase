<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',

        // Academic
        'spm_results',
        'total_as',

        // Financial
        'monthly_income',
        'income_category',

        // Study
        'study_level',
        'field_of_study',

        // Demographic
        'bumiputera',
        'citizenship',

        'age',
        'gender',
        'state',

        // Extra
        'has_leadership',
    ];

    protected $casts = [
        'spm_results' => 'array',

        'bumiputera' => 'boolean',
        'has_leadership' => 'boolean',

        'monthly_income' => 'decimal:2',

        'total_as' => 'integer',
        'age' => 'integer',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}