<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academic_category',
        'income_category',
        'study_path',
        'total_as',
        'bumiputera',
        'age',
        'gender',
        'state',
        'has_leadership',
        'spm_results'
    ];

   protected $casts = [
    'spm_results' => 'array',
    'bumiputera' => 'boolean',
    'has_leadership' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
