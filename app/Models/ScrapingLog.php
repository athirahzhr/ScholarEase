<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapingLog extends Model
{
    protected $fillable = [
        'website_name',
        'website_url',
        'pages_to_scrape',
        'status',
        'inserted_count',
        'details',
        'duration_seconds',
        'error_message',
    ];
}
