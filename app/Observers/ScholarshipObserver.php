<?php

namespace App\Observers;

use App\Models\Scholarship;

class ScholarshipObserver
{
    /**
     * Legacy rule-based categorization DISABLED.
     * System now uses confidence-based scoring.
     */
    public function created(Scholarship $scholarship)
    {
        // intentionally left blank
    }
}
