<?php
namespace App\Services;

use Carbon\Carbon;

class DeadlineExtractor
{
    public function extract(?string $text): ?Carbon
    {
        if (!$text) return null;

        // Common Malaysian formats
        $patterns = [
            '/(\d{1,2})(st|nd|rd|th)?\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{4})/i',
            '/(\d{1,2})\/(\d{1,2})\/(\d{4})/',
            '/(\d{1,2})\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{4})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                try {
                    return Carbon::parse($match[0]);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }
}
