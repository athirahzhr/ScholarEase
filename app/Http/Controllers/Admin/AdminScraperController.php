<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\ScrapingLog;

class AdminScraperController extends Controller
{
    /* =========================
     * SHOW SCRAPER PAGE
     * ========================= */
    public function index()
    {
        return view('admin.scraper.index');
    }

    /* =========================
     * STEP 1: RUN NODE SCRAPER
     * ========================= */
    public function run(Request $request)
    {
        $request->validate([
            'source' => 'required|in:jpa',
        ]);

        $start = now();

        $log = ScrapingLog::create([
            'website_name' => 'JPA',
            'website_url' => 'https://penajaan.jpa.gov.my',
            'status' => 'in_progress',
        ]);

        try {
            // 🔥 RUN NODE SCRAPER
            exec('node scrapers/jpa.js', $output, $resultCode);

            if ($resultCode !== 0) {
                throw new \Exception('Node scraper failed');
            }

            $log->update([
                'status' => 'reviewing',
                'details' => 'Scraping completed. Awaiting admin review.',
                'duration_seconds' => now()->diffInSeconds($start),
            ]);

            return redirect()
                ->route('admin.scraper.review')
                ->with('success', 'Scraping completed. Please review data.');

        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Scraping failed.');
        }
    }

    /* =========================
     * STEP 2: REVIEW SCRAPED DATA
     * ========================= */
    public function review()
    {
        $items = DB::table('scraped_scholarships')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.scraper.review', compact('items'));
    }

    /* =========================
     * STEP 3: IMPORT APPROVED
     * ========================= */
    public function import()
    {
        $start = now();

        try {
            Artisan::call('app:import-scholarships');

            $count = DB::table('scholarships')
                ->whereDate('created_at', today())
                ->count();

            ScrapingLog::where('status', 'reviewing')
                ->latest()
                ->first()
                ?->update([
                    'status' => 'success',
                    'scholarships_added' => $count,
                    'duration_seconds' => now()->diffInSeconds($start),
                ]);

            return redirect()
                ->route('admin.scraping.logs')
                ->with('success', 'Scholarships imported successfully.');

        } catch (\Throwable $e) {
            ScrapingLog::where('status', 'reviewing')
                ->latest()
                ->first()
                ?->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

            return back()->with('error', 'Import failed.');
        }
    }
}
