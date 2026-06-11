<?php

namespace App\Http\Controllers;

use App\Models\ScrapingLog;
use App\Models\Scholarship;

class ScrapingController extends Controller
{
    public function logs()
    {
        return view('admin.scraping.logs', [
            'logs' => ScrapingLog::latest()->paginate(20),

            'totalScholarships' => Scholarship::count(),
            'activeScholarships' => Scholarship::where('is_active', true)->count(),

            'todayScrapes' => ScrapingLog::whereBetween(
                'started_at',
                [
                    now('Asia/Kuala_Lumpur')->startOfDay(),
                    now('Asia/Kuala_Lumpur')->endOfDay()
                ]
            )->count(),

            'totalScrapes' => ScrapingLog::count(),

            'uncategorized' => Scholarship::whereDoesntHave('eligibilityCriteria')->count(),
        ]);
    }
}