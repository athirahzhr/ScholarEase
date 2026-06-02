<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'provider',
        'description',
        'raw_eligibility',
        'application_link',
        'deadline',
        'source',
        'source_website',
        'is_official',
        'is_active',
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_active' => 'boolean',
        'is_official' => 'boolean',
    ];

    /**
     * Get the eligibility criteria for this scholarship
     */
    public function eligibilityCriteria()
    {
        return $this->hasOne(
            ScholarshipEligibilityCriteria::class
        );
    }

    /**
     * Scope: Active scholarships only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Scholarships with upcoming deadlines
     */
    public function scopeUpcoming($query)
    {
        return $query->where(
            'deadline',
            '>=',
            now()
        );
    }

    /**
     * Scope: Filter by source
     */
    public function scopeBySource(
        $query,
        string $source
    ) {
        return $query->where('source', $source);
    }

    /**
     * Scope: Scraped scholarships
     */
    public function scopeScraped($query)
    {
        return $query->where(
            'source',
            'scraped'
        );
    }

    /**
     * Scope: Manual scholarships
     */
    public function scopeManual($query)
    {
        return $query->where(
            'source',
            'manual'
        );
    }

    /**
     * Check if deadline has passed
     */
    public function isExpired(): bool
    {
        return $this->deadline
            && $this->deadline->isPast();
    }

    /**
     * Get days until deadline
     */
    public function daysUntilDeadline(): ?int
    {
        return $this->deadline
            ? now()->diffInDays(
                $this->deadline,
                false
            )
            : null;
    }

    /**
     * Check if scholarship has eligibility
     */
    public function hasCompleteEligibility(): bool
    {
        return $this->eligibilityCriteria !== null;
    }

    /**
     * Scholarship bookmarks
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(
            \App\Models\Bookmark::class
        );
    }
}