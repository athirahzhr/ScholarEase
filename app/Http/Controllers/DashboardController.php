<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Scholarship;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

 public function index()
{
    $user = Auth::user()->load('unreadNotifications');

    // 🔵 1. ADMIN redirect ke admin dashboard
    if ($user->role === 'admin') {
        return redirect('/admin/dashboard');
    }

    // 🟡 2. USER belum verify email → redirect verify page
    if (!$user->hasVerifiedEmail()) {
        return redirect('/email/verify');
    }

    $recommendationCount = collect(
        Session::get('recommendation_ids', [])
    )->count();

    // 🟢 3. USER normal → dashboard
    return view('dashboard', compact('user', 'recommendationCount'));
}
}