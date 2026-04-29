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
            'scrape:bpmp',
            'scrape:jpa',
            'scrape:khazanah',
            'scrape:mara',
            'scrape:petronas',
            'scrape:shell',
            'scrape:yp',
        ];

        return view('admin.scraper.index', compact('commands'));
    }

    public function run(Request $request)
    {
        $request->validate([
            'command' => 'required|string'
        ]);

        try {
            Artisan::call($request->command);

            return back()->with('success', 'Scraper executed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}