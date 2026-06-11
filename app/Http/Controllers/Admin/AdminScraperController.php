<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminScraperController extends Controller
{
    public function index()
    {
        $commands = [
            'scrape:all',
            'scrape:axiata',
            'scrape:bnm',
            'scrape:bpmb',
            'scrape:k.watan',
            'scrape:k.paynet',
            'scrape:k.equity',
            'scrape:mara',
            'scrape:petronas',
            'scrape:shell',
            'scrape:jpa.db40',
            'scrape:jpa.ppn',
            'scrape:jpa.lspm',
        ];

        return view('admin.scraper.index', compact('commands'));
    }

    public function run(Request $request)
    {
        $request->validate([
            'command' => 'required|string'
        ]);

        $command = $request->command;

        try {

            // Run Scrape All in background
            if ($command === 'scrape:all') {

                shell_exec(
                    "PLAYWRIGHT_BROWSERS_PATH=/var/www/.cache/ms-playwright npm run scrape:all > /tmp/scrape_all.log 2>&1 &"
                );

                return back()->with(
                    'success',
                    '🚀 Scrape All started in background. Check Scraping Logs for progress.'
                );
            }

            // Run single scraper normally
            $output = shell_exec(
                "PLAYWRIGHT_BROWSERS_PATH=/var/www/.cache/ms-playwright npm run {$command} 2>&1"
            );

            if (!$output) {
                return back()->with(
                    'error',
                    'No output returned from scraper.'
                );
            }

            return back()->with(
                'success',
                $output
            );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}