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

        // Featured scholarships (random every login session)
        if (!Session::has('featured_scholarships')) {

            $featuredScholarships = Scholarship::where('is_active', 1)
                ->where(function ($query) {
                    $query->whereNull('deadline')
                        ->orWhere('deadline', '>=', now());
                })
                ->inRandomOrder()
                ->take(3)
                ->get();

            Session::put(
                'featured_scholarships',
                $featuredScholarships
            );

        } else {

            $featuredScholarships =
                Session::get('featured_scholarships');

        }

        // Latest feedback
        $feedbacks = Feedback::with('user')
            ->latest()
            ->take(3)
            ->get();

        // Feedback statistics
        $averageRating = Feedback::avg('rating');

        $totalFeedback = Feedback::count();

        return view(
            'dashboard',
            compact(
                'user',
                'recommendationCount',
                'featuredScholarships',
                'feedbacks',
                'averageRating',
                'totalFeedback'
            )
        );
}
}