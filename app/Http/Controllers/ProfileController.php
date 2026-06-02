<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Show profile
     */
    public function index()
    {
        $profile = Auth::user()->profile;

        return view(
            'profile.index',
            compact('profile')
        );
    }

    /**
     * Show profile form
     */
    public function create()
    {
        return view('profile.create');
    }

    /**
     * Store / update profile
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

        // Academic
        'total_as' => 'required|integer|min:0|max:12',

        // Financial
        'income_category' => 'required|in:B40,M40,T20',

        // Study
        'study_path' => 'required|string|max:100',

        'field_of_study' => 'required|string|max:255',

        // Demographic
        'bumiputera' => 'required|boolean',

        'citizenship' => 'required|string|max:100',

        'age' => 'required|integer|min:15|max:100',

        'gender' => 'required|in:Male,Female',

        'state' => 'required|string|max:100',

        // Extra
        'has_leadership' => 'required|boolean',
    ]);

    // ================= INCOME CONVERSION =================

    switch ($request->income_category) {

        case 'B40':
            $monthlyIncome = 5351;
            break;

        case 'M40':
            $monthlyIncome = 11161;
            break;

        case 'T20':
            $monthlyIncome = 11819;
            break;

        default:
            $monthlyIncome = null;
    }

    UserProfile::updateOrCreate(

        ['user_id' => Auth::id()],

        [

            // Academic
            'total_as' => $validated['total_as'],

            // Financial
            'income_category' =>
                $validated['income_category'],

            'monthly_income' =>
                $monthlyIncome,

            // Study
            'study_level' =>
                $validated['study_path'],

            'field_of_study' =>
                $validated['field_of_study'],

            // Demographic
            'bumiputera' =>
                $validated['bumiputera'],

            'citizenship' =>
                $validated['citizenship'],

            'age' =>
                $validated['age'],

            'gender' =>
                $validated['gender'],

            'state' =>
                $validated['state'],

            // Extra
            'has_leadership' =>
                $validated['has_leadership'],
        ]
    );

    return redirect()
        ->route('scholarship.recommendations')
        ->with(
            'success',
            'Profile saved successfully!'
        );

    }

}