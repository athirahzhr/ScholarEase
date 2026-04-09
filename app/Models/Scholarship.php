<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    'is_active',
];


   protected $casts = [
     'deadline' => 'date',
    'is_active' => 'boolean',
  
];


    /**
     * Get the eligibility criteria for this scholarship
     */
   public function eligibilityCriteria()
{
    return $this->hasOne(ScholarshipEligibilityCriteria::class);
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
        return $query->where('deadline', '>=', now());
    }

    /**
     * Scope: Filter by source
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope: Scraped scholarships
     */
    public function scopeScraped($query)
    {
        return $query->where('source', 'scraped');
    }

    /**
     * Scope: Manual scholarships
     */
    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }

    /**
     * Check if scholarship matches student profile using stored procedure
     * This is the PREFERRED method for matching
     */
    public static function findMatches(array $studentProfile)
    {
        return DB::select('CALL find_matching_scholarships(?, ?, ?, ?, ?, ?, ?, ?)', [
            $studentProfile['total_as'],
            $studentProfile['income_category'],
            $studentProfile['study_path'],
            $studentProfile['bumiputera'],
            $studentProfile['age'],
            $studentProfile['gender'],
            $studentProfile['state'],
            $studentProfile['has_leadership'],
        ]);
    }

    /**
     * Alternative: PHP-based matching (slower but more flexible)
     */
    public static function findMatchesPHP(array $studentProfile)
    {
        return self::active()
            ->with('eligibilityCriteria')
            ->get()
            ->filter(function($scholarship) use ($studentProfile) {
                if (!$scholarship->eligibilityCriteria) {
                    return false;
                }
                return $scholarship->eligibilityCriteria->matches($studentProfile);
            });
    }

    /**
     * Get match percentage for a student (0-100)
     */
    public function getMatchPercentage(array $studentProfile): int
    {
        if (!$this->eligibilityCriteria) {
            return 0;
        }

        $breakdown = $this->eligibilityCriteria->getMatchBreakdown($studentProfile);
        $total = count($breakdown);
        $matches = count(array_filter($breakdown));

        return $total > 0 ? (int)(($matches / $total) * 100) : 0;
    }

    /**
     * Check if scholarship has complete eligibility data
     */
    public function hasCompleteEligibility(): bool
    {
        return $this->eligibilityCriteria !== null 
            && $this->eligibilityCriteria->min_spm_as !== null;
    }

    /**
     * Get human-readable academic requirement
     */
 



    /**
     * Helper: Get category label
     */


    /**
     * Check if deadline has passed
     */
    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    /**
     * Get days until deadline
     */
    public function daysUntilDeadline(): ?int
    {
        return $this->deadline 
            ? now()->diffInDays($this->deadline, false) 
            : null;
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(\App\Models\Bookmark::class);
    }

    
    
}