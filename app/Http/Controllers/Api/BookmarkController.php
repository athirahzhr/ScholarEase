<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $bookmarks = Bookmark::where('user_id', $request->user()->id)
            ->with('scholarship')
            ->latest()
            ->get();

        return response()->json([
            'bookmarks' => $bookmarks
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $bookmark = Bookmark::findOrFail($id);

        if ($bookmark->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bookmark->delete();

        return response()->json([
            'message' => 'Bookmark deleted successfully'
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'scholarship_id' => 'required|exists:scholarships,id'
    ]);

    $existing = Bookmark::where('user_id', $request->user()->id)
        ->where('scholarship_id', $request->scholarship_id)
        ->first();

    if ($existing) {
        return response()->json([
            'message' => 'Already bookmarked'
        ], 200);
    }

    $bookmark = Bookmark::create([
        'user_id' => $request->user()->id,
        'scholarship_id' => $request->scholarship_id,
    ]);

    return response()->json([
        'success' => true,
        'bookmark' => $bookmark
    ], 201);
}
}