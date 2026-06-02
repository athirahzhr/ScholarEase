<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProfile;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // GET PROFILE
    public function index(Request $request)
    {
        $profile = UserProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'profile' => null
            ]);
        }

        return response()->json([
            'profile' => [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'total_as' => $profile->total_as,
                'bumiputera' => $profile->bumiputera,
                'age' => $profile->age,
                'gender' => $profile->gender,
                'state' => $profile->state,
                'has_leadership' => $profile->has_leadership,
                'created_at' => $profile->created_at,
                'updated_at' => $profile->updated_at,
            ]
        ]);
    }

    // CREATE / UPDATE PROFILE
    // CREATE / UPDATE PROFILE
public function store(Request $request)
{
    $request->validate([
        'total_as' => 'nullable',
        'age' => 'nullable',
        'gender' => 'nullable',
        'state' => 'nullable',
    ]);

    /// FIND EXISTING PROFILE
    $profile = UserProfile::firstOrNew([
        'user_id' => $request->user()->id
    ]);

    /// PRESERVE OLD DATA
    $profile->academic_category =
        $request->academic_category
        ?? $profile->academic_category;

    $profile->income_category =
        $request->income_category
        ?? $profile->income_category;

    $profile->study_path =
        $request->study_path
        ?? $profile->study_path;

    $profile->spm_results =
        $request->spm_results
        ?? $profile->spm_results;

    $profile->total_as =
        $request->total_as
        ?? $profile->total_as;

    $profile->bumiputera =
        $request->bumiputera
        ?? $profile->bumiputera;

    $profile->age =
        $request->age
        ?? $profile->age;

    $profile->gender =
        $request->gender
        ?? $profile->gender;

    $profile->state =
        $request->state
        ?? $profile->state;

    $profile->has_leadership =
        $request->has_leadership
        ?? $profile->has_leadership;

    $profile->save();

    return response()->json([
        'message' => 'Profile saved successfully',
        'profile' => $profile
    ]);
}
}