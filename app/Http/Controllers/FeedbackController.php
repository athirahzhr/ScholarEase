<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function create()
    {
        return view('feedback.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
    
        ]);

        return redirect()
            ->back()
            ->with('success', 'Thank you for your feedback!');
    }
}