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

    $recommendationCount = collect(
        Session::get('recommendation_ids', [])
    )->count();

    return view('dashboard', compact('user', 'recommendationCount'));
}
}