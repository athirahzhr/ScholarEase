<?php
namespace App\Services;

use App\Models\Scholarship;

class ScholarshipPostProcessor
{
    public function processDeadlines(): int
    {
        $extractor = app(DeadlineExtractor::class);
        $updated = 0;

        Scholarship::where('source', 'scraped')
            ->whereNull('deadline')
            ->chunk(50, function ($scholarships) use ($extractor, &$updated) {

                foreach ($scholarships as $s) {

                    $text = strtolower(
                        ($s->raw_eligibility ?? '') . ' ' .
                        ($s->description ?? '')
                    );

                    $deadline = $extractor->extract($text);

                    if ($deadline) {
                        $s->deadline = $deadline;
                        $s->save();
                        $updated++;
                    }
                }
            });

        return $updated;
    }
}
