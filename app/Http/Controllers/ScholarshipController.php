<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\UserProfile;
use App\Services\ScholarshipRuleMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    /**
     * Show scholarship finder form
     */
    public function showFinder()
    {
        return view('scholarship.finder');
    }

    /**
     * Get scholarship recommendations
     */
    public function getRecommendations(
        ScholarshipRuleMatcher $matcher
    ) {
        $profile = Auth::user()->profile;

        if (!$profile) {
            return redirect()
                ->route('scholarship.finder')
                ->with('error', 'Please complete your profile first');
        }

        $scholarships = Scholarship::with('eligibilityCriteria')
            ->where('is_active', true)
            ->get();

        $results = $matcher->getRecommendations(
            $profile,
            $scholarships
        );

        Session::put(
            'recommendation_ids',
            $results->pluck('id')->toArray()
        );

        return view('scholarship.recommendations', [
            'results' => $results,
            'matchCount' => $results->count(),
        ]);
    }

    /**
     * Save student profile
     */
    public function saveProfile(Request $request)
    {
        $validated = $request->validate([
            'monthly_income' => 'required|numeric|min:0',

            'study_level' => 'required|string|max:100',
            'field_of_study' => 'required|string|max:255',

            'bumiputera' => 'required|boolean',
            'citizenship' => 'required|string|max:100',

            'age' => 'required|integer|min:15|max:30',
            'gender' => 'required|in:Male,Female',

            'state' => 'required|string|max:100',

            'has_leadership' => 'required|boolean',
        ]);

        $user = Auth::user();

        $verifiedData = Session::get('verified_ocr_data', []);

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'monthly_income' => $validated['monthly_income'],

                'study_level' => $validated['study_level'],
                'field_of_study' => $validated['field_of_study'],

                'bumiputera' => $validated['bumiputera'],
                'citizenship' => $validated['citizenship'],

                'age' => $validated['age'],
                'gender' => $validated['gender'],

                'state' => $validated['state'],

                'has_leadership' => $validated['has_leadership'],

                'total_as' => $verifiedData['total_as'] ?? 0,
                'spm_results' => $verifiedData['grades'] ?? [],
            ]
        );

        Session::forget([
            'ocr_temp_data',
            'verified_ocr_data'
        ]);

        return redirect()
            ->route('scholarship.recommendations')
            ->with('success', 'Profile saved successfully!');
    }

    /**
     * Admin - scholarship list
     */
    public function index()
    {
        $scholarships = Scholarship::withCount(['bookmarks'])
            ->latest()
            ->paginate(10);

        return view(
            'admin.scholarships.index',
            compact('scholarships')
        );
    }

    /**
     * Admin - create form
     */
    public function create()
    {
        return view('admin.scholarships.create');
    }

    /**
     * Store scholarship
     */
    public function store(Request $request)
    {
        $request->validate([
            // scholarship
            'title' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'description' => 'required|string',

            'raw_eligibility' => 'nullable|string',

            'deadline' => 'nullable|date',

            'application_link' => 'nullable|url',

            'is_active' => 'required|boolean',
            'is_official' => 'required|boolean',

            // eligibility
            'min_spm_as' => 'nullable|integer|min:0|max:12',
            'max_spm_as' => 'nullable|integer|min:0|max:12',

            'required_subjects' => 'nullable|array',

            'max_monthly_income' => 'nullable|numeric',

            'study_paths' => 'nullable|array',
            'fields_of_study' => 'nullable|array',

            'gender_requirement' => 'nullable|in:Any,Male,Female',

            'citizenship_required' => 'nullable|string|max:100',
            'state_requirement' => 'nullable|string|max:100',

            'min_age' => 'nullable|integer|min:15|max:100',
            'max_age' => 'nullable|integer|min:15|max:100',

            'bumiputera_required' => 'nullable|boolean',
            'bumiputera_priority' => 'nullable|boolean',

            'leadership_required' => 'nullable|boolean',
            'leadership_priority' => 'nullable|boolean',

            'priority_weight' => 'nullable|integer|min:1|max:10',
        ]);

        DB::beginTransaction();

        try {

            // CREATE SCHOLARSHIP
            $scholarship = Scholarship::create([
                'title' => $request->title,
                'provider' => $request->provider,
                'description' => $request->description,

                'raw_eligibility' => $request->raw_eligibility,

                'deadline' => $request->deadline,

                'application_link' => $request->application_link,

                'is_active' => $request->is_active,
                'is_official' => $request->is_official,

                'source' => 'manual',
            ]);

            // CREATE ELIGIBILITY
            $scholarship->eligibilityCriteria()->create([

                'min_spm_as' => $request->min_spm_as,
                'max_spm_as' => $request->max_spm_as,

                'required_subjects' => $request->required_subjects ?? [],

                'max_monthly_income' => $request->max_monthly_income,

                'study_paths' => $request->study_paths ?? [],
                'fields_of_study' => $request->fields_of_study ?? [],

                'gender_requirement' => $request->gender_requirement ?? 'Any',

                'citizenship_required' => $request->citizenship_required,

                'state_requirement' => $request->state_requirement,

                'min_age' => $request->min_age,
                'max_age' => $request->max_age,

                'bumiputera_required' => $request->boolean('bumiputera_required'),
                'bumiputera_priority' => $request->boolean('bumiputera_priority'),

                'leadership_required' => $request->boolean('leadership_required'),
                'leadership_priority' => $request->boolean('leadership_priority'),

                'priority_weight' => $request->priority_weight ?? 1,

                'max_score' => 100,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.scholarships.index')
                ->with(
                    'success',
                    'Scholarship created successfully'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show scholarship
     */
    public function show($id)
    {
        $scholarship = Scholarship::findOrFail($id);

        return view(
            'admin.scholarships.show',
            compact('scholarship')
        );
    }

    /**
     * Edit scholarship
     */
    public function edit($id)
    {
        $scholarship = Scholarship::findOrFail($id);

        return view(
            'admin.scholarships.edit',
            compact('scholarship')
        );
    }

    /**
     * Update scholarship
     */
    public function update(
    Request $request,
    $id
) {

    $request->validate([

        'title' => 'required|string',

        'provider' => 'required|string',

        'description' => 'required|string',

        'raw_eligibility' => 'nullable|string',

        'deadline' => 'nullable|date',

        'study_paths' => 'nullable|array',

        'fields_of_study' => 'nullable|array',
    ]);

    DB::beginTransaction();

    try {

        $scholarship =
            Scholarship::findOrFail($id);

        $oldCriteria =
            $scholarship->eligibilityCriteria;

        // ================= UPDATE SCHOLARSHIP =================

        $scholarship->update([

            'title' => $request->title,

            'provider' => $request->provider,

            'description' => $request->description,

            'raw_eligibility' => $request->raw_eligibility,

            'deadline' => $request->deadline,

            'application_link' => $request->application_link,

            'is_active' => $request->boolean('is_active'),

            'is_official' => $request->boolean('is_official'),
        ]);

        // ================= ELIGIBILITY DATA =================

        $criteriaData =
            $this->getEligibilityCriteriaData(
                $request,
                $oldCriteria
            );

        $criteriaData['scholarship_id'] =
            $scholarship->id;

        // ================= CREATE OR UPDATE ELIGIBILITY =================

        \App\Models\ScholarshipEligibilityCriteria
            ::updateOrCreate(

                [
                    'scholarship_id' =>
                        $scholarship->id
                ],

                $criteriaData
            );

        DB::commit();

        return redirect()
            ->route('admin.scholarships.index')
            ->with(
                'success',
                'Scholarship updated successfully'
            );

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with(
                'error',
                $e->getMessage()
            );
    }
}

    /**
     * Eligibility update helper
     */
    protected function getEligibilityCriteriaData(
        Request $request,
        $oldCriteria
    ) {
        return [

            'min_spm_as' => $request->input(
                'min_spm_as',
                $oldCriteria->min_spm_as ?? null
            ),

            'max_spm_as' => $request->input(
                'max_spm_as',
                $oldCriteria->max_spm_as ?? null
            ),

            'required_subjects' => $request->filled('required_subjects')
                ? $request->input('required_subjects')
                : ($oldCriteria->required_subjects ?? []),

            'max_monthly_income' => $request->input(
                'max_monthly_income',
                $oldCriteria->max_monthly_income ?? null
            ),

            'study_paths' => $request->filled('study_paths')
                ? $request->input('study_paths')
                : ($oldCriteria->study_paths ?? []),

            'fields_of_study' => $request->filled('fields_of_study')
                ? $request->input('fields_of_study')
                : ($oldCriteria->fields_of_study ?? []),

            'min_age' => $request->input(
                'min_age',
                $oldCriteria->min_age ?? null
            ),

            'max_age' => $request->input(
                'max_age',
                $oldCriteria->max_age ?? null
            ),

            'gender_requirement' => $request->input(
                'gender_requirement',
                $oldCriteria->gender_requirement ?? 'Any'
            ),

            'citizenship_required' => $request->input(
                'citizenship_required',
                $oldCriteria->citizenship_required ?? null
            ),

            'state_requirement' => $request->input(
                'state_requirement',
                $oldCriteria->state_requirement ?? null
            ),

            'bumiputera_required' => $request->boolean(
                'bumiputera_required',
                $oldCriteria->bumiputera_required ?? false
            ),

            'bumiputera_priority' => $request->boolean(
                'bumiputera_priority',
                $oldCriteria->bumiputera_priority ?? false
            ),

            'leadership_required' => $request->boolean(
                'leadership_required',
                $oldCriteria->leadership_required ?? false
            ),

            'leadership_priority' => $request->boolean(
                'leadership_priority',
                $oldCriteria->leadership_priority ?? false
            ),

            'priority_weight' => $request->input(
                'priority_weight',
                $oldCriteria->priority_weight ?? 1
            ),

            'max_score' => 100,
        ];
    }

    /**
     * Delete scholarship
     */
    public function destroy($id)
    {
        try {

            $scholarship = Scholarship::findOrFail($id);

            $scholarship->delete();

            return redirect()
                ->route('admin.scholarships.index')
                ->with(
                    'success',
                    'Scholarship deleted successfully!'
                );

        } catch (\Exception $e) {

            return redirect()
                ->route('admin.scholarships.index')
                ->with(
                    'error',
                    'Failed to delete scholarship: '
                    . $e->getMessage()
                );
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $scholarship = Scholarship::findOrFail($id);

        $scholarship->update([
            'is_active' => !$scholarship->is_active
        ]);

        $status = $scholarship->is_active
            ? 'activated'
            : 'deactivated';

        return redirect()
            ->route('admin.scholarships.show', $scholarship)
            ->with(
                'success',
                'Scholarship ' . $status . ' successfully!'
            );
    }

    /**
     * Public browse
     */
    public function browse()
    {
        $scholarships = Scholarship::active()
            ->upcoming()
            ->latest()
            ->paginate(12);

        return view(
            'scholarships.browse',
            compact('scholarships')
        );
    }

    /**
     * Search scholarships
     */
    public function search(Request $request)
    {
        $query = Scholarship::active()
            ->upcoming();

        if ($request->has('search')) {

            $search = $request->get('search');

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('provider', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $scholarships = $query
            ->latest()
            ->paginate(12);

        return view(
            'scholarships.search',
            compact('scholarships')
        );
    }

    /**
     * Public scholarship detail
     */
    public function showPublic($id)
    {
        $scholarship = Scholarship::findOrFail($id);

        return view(
            'scholarship.show',
            compact('scholarship')
        );
    }

    /**
     * Admin detail
     */
    public function showAdmin($id)
    {
        $scholarship = Scholarship::withCount(['bookmarks'])
            ->findOrFail($id);

        return view(
            'admin.scholarships.show',
            compact('scholarship')
        );
    }

    /**
     * API
     */
    public function apiIndex()
    {
        $scholarships = Scholarship::select(
            'id',
            'title',
            'provider',
            'description'
        )->get();

        return response()->json($scholarships);
    }
}