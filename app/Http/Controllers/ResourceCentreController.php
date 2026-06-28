<?php

namespace App\Http\Controllers;

use App\Models\ResourceVideo;

class ResourceCentreController extends Controller
{
    public function index()
    {
        $journey = ResourceVideo::where(
            'category',
            'Scholarship Journey'
        )
        ->where('is_active', true)
        ->latest()
        ->get();

        $tips = ResourceVideo::where(
            'category',
            'Scholarship Tips'
        )
        ->where('is_active', true)
        ->latest()
        ->get();

        $interview = ResourceVideo::where(
            'category',
            'Scholarship Interview'
        )
        ->where('is_active', true)
        ->latest()
        ->get();

        return view(
            'resource-centre.index',
            compact(
                'journey',
                'tips',
                'interview'
            )
        );
    }
}