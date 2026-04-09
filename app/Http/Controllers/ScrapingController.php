<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JpaScraper;
use App\Services\KhazanahScraper;
use App\Services\UnienrolScraper;
use App\Models\ScrapingLog;
use App\Models\Scholarship;
use Illuminate\Support\Facades\Log;

class ScrapingController extends Controller
{
    protected $jpa;
    protected $khazanah;
    protected $unienrol;

    public function __construct(
        JpaScraper $jpa,
        KhazanahScraper $khazanah,
        UnienrolScraper $unienrol
    ) {
        $this->jpa = $jpa;
        $this->khazanah = $khazanah;
        $this->unienrol = $unienrol;
    }

    /**
     * Scraping logs dashboard
     */
    public function logs()
{
    return view('admin.scraping.logs', [
        'logs' => ScrapingLog::latest()->paginate(20),

        // statistics
        'totalScholarships'  => Scholarship::count(),
        'activeScholarships' => Scholarship::where('is_active', true)->count(),

        // NEW meaning: no structured eligibility rules yet
        'uncategorized' => Scholarship::whereDoesntHave('eligibilityCriteria')->count(),
    ]);
}

    /**
     * Run JPA scraper
     */
    public function scrapeJPA()
{
    $start = microtime(true);

    try {
        $data = $this->jpa->scrape();
        $saved = $this->jpa->saveToDatabase($data);

        ScrapingLog::create([
            'website_name'       => 'JPA',
            'website_url'        => 'https://esilav2.jpa.gov.my/',
            'status'             => 'success',
            'scholarships_added' => $saved,
            'details'            => 'JPA scraping completed successfully',
            'duration_seconds'   => (int) (microtime(true) - $start),
        ]);

        return back()->with('success', "JPA scraping completed ({$saved} records).");

    } catch (\Throwable $e) {

        ScrapingLog::create([
            'website_name'     => 'JPA',
            'website_url'      => 'https://esilav2.jpa.gov.my/',
            'status'           => 'failed',
            'error_message'    => $e->getMessage(),
            'duration_seconds'=> (int) (microtime(true) - $start),
        ]);

        return back()->with('error', 'JPA scraping failed.');
    }
}


    /**
     * Run Khazanah scraper
     */
    public function scrapeKhazanah()
    {
        $start = microtime(true);

        try {
            $data = $this->khazanah->scrape();
            $saved = $this->khazanah->saveToDatabase($data);

           ScrapingLog::create([
                'website_name'       => 'Yayasan Khazanah',
                'website_url'        => 'https://www.yayasankhazanah.com.my/',
                'status'             => 'success',
                'scholarships_added' => $saved,
                'details'            => 'Khazanah scraping completed',
                'duration_seconds'   => (int) (microtime(true) - $start),
            ]);


            return back()->with('success', "Khazanah scraping completed ({$saved} records).");

        } catch (\Exception $e) {

            ScrapingLog::create([
                'website_name'       => 'Yayasan Khazanah',
                'website_url'        => 'https://www.yayasankhazanah.com.my/',
                'pages_to_scrape'    => null,
                'status'             => 'failed',
                'scholarships_added' => 0,
                'error_message'      => $e->getMessage(),
                'duration_seconds'   => round(microtime(true) - $start),
            ]);

            Log::error($e->getMessage());
            return back()->with('error', 'Khazanah scraping failed.');
        }
    }

    /**
     * Run Unienrol scraper
     */
    public function scrapeUnienrol()
    {
        $start = microtime(true);

        try {
            $data = $this->unienrol->scrape();
            $saved = $this->unienrol->saveToDatabase($data);

            ScrapingLog::create([
                'website_name'       => 'Unienrol',
                'website_url'        => 'https://unienrol.com/',
                'status'             => 'success',
                'scholarships_added' => $saved,
                'details'            => 'Unienrol scraping completed',
                'duration_seconds'   => (int) (microtime(true) - $start),
            ]);


            return back()->with('success', "Unienrol scraping completed ({$saved} records).");

        } catch (\Exception $e) {

            ScrapingLog::create([
                'website_name'       => 'Unienrol',
                'website_url'        => 'https://unienrol.com/',
                'pages_to_scrape'    => null,
                'status'             => 'failed',
                'scholarships_added' => 0,
                'error_message'      => $e->getMessage(),
                'duration_seconds'   => round(microtime(true) - $start),
            ]);

            Log::error($e->getMessage());
            return back()->with('error', 'Unienrol scraping failed.');
        }
    }
}
