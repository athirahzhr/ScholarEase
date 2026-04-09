<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        // Use Bookmark model directly instead of user->bookmarks()
        $bookmarks = Bookmark::where('user_id', Auth::id())
            ->with('scholarship')
            ->latest()
            ->get();
        
        return view('bookmarks.index', compact('bookmarks'));
    }
    
    public function toggle(Request $request, $id)
    {
        $request->validate([
            '_token' => 'required'
        ]);
        
        $user = Auth::user();
        $scholarship = Scholarship::findOrFail($id);
        
        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('scholarship_id', $scholarship->id)
            ->first();
        
        if ($bookmark) {
            $bookmark->delete();
            $message = 'Scholarship removed from bookmarks.';
            $isBookmarked = false;
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'scholarship_id' => $scholarship->id,
                
            ]);
            $message = 'Scholarship added to bookmarks.';
            $isBookmarked = true;
        }
        
        // Count bookmarks without using user->bookmarks()
        $bookmarksCount = Bookmark::where('user_id', $user->id)->count();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'isBookmarked' => $isBookmarked,
                'bookmarksCount' => $bookmarksCount
            ]);
        }
        
        return redirect()->back()->with([
            'success' => $message,
            'isBookmarked' => $isBookmarked
        ]);
    }
    
    public function destroy($id)
    {
        $bookmark = Bookmark::findOrFail($id);
        
        // Check if the bookmark belongs to the current user
        if ($bookmark->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $bookmark->delete();
        
        return redirect()->route('bookmarks.index')
            ->with('success', 'Bookmark removed successfully.');
    }
}