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
        'monthly_income' => 'required|numeric|min:0',

        // Study
        'study_path' => 'required|string|max:100',

        'field_of_study' => 'required|string|max:255',

        // Demographic
        'bumiputera' => 'required|boolean',

        'citizenship' => 'required|string|max:100',

        'age' => 'required|integer|min:15|max:100',


        'state' => 'required|string|max:100',

        // Extra
        'has_leadership' => 'required|boolean',
    ]);

    // ================= INCOME CONVERSION =================


    $monthlyIncome = $validated['monthly_income'];

    $incomeCategory =
        $this->deriveIncomeCategory(
            $monthlyIncome
        );

    UserProfile::updateOrCreate(

        ['user_id' => Auth::id()],

        [

            // Academic
            'total_as' => $validated['total_as'],

            // Financial
            'income_category' => $incomeCategory,

            'monthly_income' => $monthlyIncome,

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

    private function deriveIncomeCategory($income)
    {
        if ($income <= 4850) {
            return 'B40';
        }

        if ($income <= 10960) {
            return 'M40';
        }

        return 'T20';
    }

}

