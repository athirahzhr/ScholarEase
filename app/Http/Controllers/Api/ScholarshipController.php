<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Scholarship;

class ScholarshipController extends Controller
{
    /// GET ALL SCHOLARSHIPS
    public function index()
    {
        $scholarships = Scholarship::select(
            'id',
            'title',
            'provider',
            'description',
            'application_link',
            'deadline'
        )->get();

        return response()->json([
            'success' => true,
            'data' => $scholarships
        ]);
    }

    /// RECOMMENDATION SYSTEM
    public function recommendations(Request $request)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Complete profile first'
            ], 400);
        }

        $matchedIds = DB::select(
            'CALL filter_scholarships(?, ?, ?, ?)',
            [
                $profile->total_as,
                $profile->income_category,
                $profile->study_path,
                $profile->bumiputera,
            ]
        );

        if (empty($matchedIds)) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $scholarships = Scholarship::whereIn(
            'id',
            collect($matchedIds)->pluck('id')
        )->get();

        return response()->json([
            'success' => true,
            'data' => $scholarships
        ]);
    }

    /// SHOW SINGLE SCHOLARSHIP
    public function show($id)
    {
        $scholarship = Scholarship::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $scholarship
        ]);
    }
}