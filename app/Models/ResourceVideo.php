<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'youtube_url',
        'description',
        'is_active',
    ];

    public function getYoutubeIdAttribute()
    {
        parse_str(
            parse_url($this->youtube_url, PHP_URL_QUERY),
            $query
        );

        return $query['v'] ?? null;
    }
}