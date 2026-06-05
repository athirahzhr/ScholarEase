<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

        // run scraper and capture output
        $output = shell_exec(
        "PLAYWRIGHT_BROWSERS_PATH=/root/.cache/ms-playwright npm run {$command} 2>&1"
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