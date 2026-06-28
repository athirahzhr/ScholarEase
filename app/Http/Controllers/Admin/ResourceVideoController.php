<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceVideo;
use Illuminate\Http\Request;

class ResourceVideoController extends Controller
{
    /**
     * Display all videos.
     */
    public function index()
    {
        $videos = ResourceVideo::latest()->paginate(10);

        return view(
            'admin.resource-videos.index',
            compact('videos')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.resource-videos.create');
    }

    /**
     * Store new video.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Scholarship Journey,Scholarship Tips,Scholarship Interview',
            'youtube_url' => 'required|url',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        ResourceVideo::create($validated);

        return redirect()
            ->route('admin.resource-videos.index')
            ->with('success', 'Video added successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(ResourceVideo $resourceVideo)
    {
        return view(
            'admin.resource-videos.edit',
            compact('resourceVideo')
        );
    }

    /**
     * Update video.
     */
    public function update(Request $request, ResourceVideo $resourceVideo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Scholarship Journey,Scholarship Tips,Scholarship Interview',
            'youtube_url' => 'required|url',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $resourceVideo->update($validated);

        return redirect()
            ->route('admin.resource-videos.index')
            ->with('success', 'Video updated successfully.');
    }

    /**
     * Delete video.
     */
    public function destroy(ResourceVideo $resourceVideo)
    {
        $resourceVideo->delete();

        return redirect()
            ->route('admin.resource-videos.index')
            ->with('success', 'Video deleted successfully.');
    }
}