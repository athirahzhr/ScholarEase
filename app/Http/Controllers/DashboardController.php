<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Scholarship;
use App\Models\Feedback;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user()->load('unreadNotifications');

        // ADMIN
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        $recommendationCount = collect(
            Session::get('recommendation_ids', [])
        )->count();

        // Latest approved feedback
        $feedbacks = Feedback::with('user')
            ->where('approved', 1)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Feedback statistics
        $averageRating = Feedback::where('approved', 1)->avg('rating');

        $totalFeedback = Feedback::where('approved', 1)->count();

        return view(
            'dashboard',
            compact(
                'user',
                'recommendationCount',
                'feedbacks',
                'averageRating',
                'totalFeedback'
            )
        );
}
}