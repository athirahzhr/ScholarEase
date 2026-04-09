<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapedScholarship extends Model
{
    protected $fillable = [
        'website_name',
        'title',
        'provider',
        'application_link',
        'deadline',
        'raw_eligibility',
        'rules',
        'status',
    ];

    protected $casts = [
        'rules' => 'array',
        'deadline' => 'date',
    ];
}
