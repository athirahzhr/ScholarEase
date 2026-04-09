<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ScholarshipController extends Controller
{
    // Public methods for users
    public function showFinder()
    {
        return view('scholarship.finder');
    }
    
public function getRecommendations()
{
    $profile = Auth::user()->profile;

    if (!$profile) {
        return redirect()
            ->route('scholarship.finder')
            ->with('error', 'Please complete your profile first');
    }

    $student = [
        'total_as' => $profile->total_as,
        'income_category' => $profile->income_category,
        'study_path' => $profile->study_path,
        'bumiputera' => (bool) $profile->bumiputera,
    ];

    // STEP 1: STRICT FILTER
    $matchedIds = DB::select(
        'CALL filter_scholarships(?, ?, ?, ?)',
        [
            $student['total_as'],
            $student['income_category'],
            $student['study_path'],
            $student['bumiputera'],
        ]
    );

    // ✅ NO MATCH → RETURN TERUS
    if (empty($matchedIds)) {
        Session::forget('recommendation_ids');

        return view('scholarship.recommendations', [
            'results' => collect(),
            'matchCount' => 0,
        ]);
    }

    // ✅ MATCH EXISTS
    $scholarships = Scholarship::whereIn(
        'id',
        collect($matchedIds)->pluck('id')
    )->get();

    // ✅ SIMPAN KE SESSION (SEKARANG CONFIRM EXIST)
    Session::put(
        'recommendation_ids',
        $scholarships->pluck('id')->toArray()
    );

    return view('scholarship.recommendations', [
        'results' => $scholarships,
        'matchCount' => $scholarships->count(),
    ]);
}


    
    public function saveProfile(Request $request)
    {
        $validated = $request->validate([
            'academic_category' => 'required|in:A1,A2,A3,A4',
            'income_category' => 'required|in:B1,B3,B4',
            'study_path' => 'required|in:C1,C2,C3,C4',
            'bumiputera' => 'required|boolean',
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
                'academic_category' => $validated['academic_category'],
                'income_category' => $validated['income_category'],
                'study_path' => $validated['study_path'],
                'bumiputera' => $validated['bumiputera'],
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'state' => $validated['state'],
                'has_leadership' => $validated['has_leadership'],
                'total_as' => $verifiedData['total_as'] ?? 0,
                'spm_results' => $verifiedData['grades'] ?? []
            ]
        );

        Session::forget(['ocr_temp_data', 'verified_ocr_data']);

        return redirect()->route('scholarship.recommendations')
            ->with('success', 'Profile saved successfully!');
    }

    
    // Admin methods for managing scholarships
    
    /**
     * Display a listing of scholarships.
     */
    public function index()
    {
        $scholarships = Scholarship::withCount(['bookmarks'])
            ->latest()
            ->paginate(10);
        return view('admin.scholarships.index', compact('scholarships'));
    }
    
    /**
     * Show the form for creating a new scholarship.
     */
    public function create()
    {
        return view('admin.scholarships.create');
    }
    
    /**
     * Store a newly created scholarship in storage.
     */
 public function store(Request $request)
{
    $request->validate([
        'title'       => 'required|string|max:255',
        'provider'    => 'required|string|max:255',
        'description' => 'required|string',
        'raw_eligibility' => 'nullable|string',

        'deadline' => 'nullable|date',
        'application_link' => 'nullable|url',
        'is_active' => 'required|boolean',
        'is_official' => 'required|boolean',

        // eligibility
        'min_spm_as' => 'nullable|integer|min:0|max:12',
        'max_spm_as' => 'nullable|integer|min:0|max:12',
        'academic_categories' => 'nullable|array',
        'income_categories' => 'nullable|array',
        'study_paths' => 'nullable|array',
        'max_monthly_income' => 'nullable|numeric',
        'gender_requirement' => 'nullable|in:Any,Male,Female',
    ]);

    DB::beginTransaction();

    try {
        // 1️⃣ CREATE SCHOLARSHIP
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

        // 2️⃣ MAP FORM → SYSTEM CODES
        $incomeMap = [
            'B40' => 'B1',
            'M40' => 'B3',
            'T20' => 'B4',
        ];

        $studyMap = [
            'Pre-University' => 'C1',
            'Diploma' => 'C2',
            'Degree' => 'C3',
            'TVET' => 'C4',
        ];

        // 3️⃣ CREATE ELIGIBILITY (INILAH YANG HILANG SEBELUM NI)
        $scholarship->eligibilityCriteria()->create([
            'min_spm_as' => $request->min_spm_as,
            'max_spm_as' => $request->max_spm_as,

            'academic_categories' => $request->academic_categories ?? [],

            'income_categories' => collect($request->income_categories ?? [])
                ->map(fn($v) => $incomeMap[$v] ?? null)
                ->filter()
                ->values()
                ->toArray(),

            'study_paths' => collect($request->study_paths ?? [])
                ->map(fn($v) => $studyMap[$v] ?? null)
                ->filter()
                ->values()
                ->toArray(),

            'max_monthly_income' => $request->max_monthly_income,
            'gender_requirement' => $request->gender_requirement ?? 'Any',

            'match_all_criteria' => true,
            'priority_weight' => 1,
        ]);

        DB::commit();

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', 'Scholarship & eligibility created successfully');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', 'Create failed: ' . $e->getMessage());
    }
}

    
    /**
     * Display the specified scholarship.
     */
    public function show($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        return view('admin.scholarships.show', compact('scholarship'));
    }
    
    /**
     * Show the form for editing the specified scholarship.
     */
    public function edit($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        return view('admin.scholarships.edit', compact('scholarship'));
    }
    
    /**
     * Update the specified scholarship in storage.
     */

 public function update(Request $request, Scholarship $scholarship)
    {

   



        $request->validate([
            'title'       => 'required|string',
            'provider'    => 'required|string',
            'description' => 'required|string',
            'raw_eligibility' => 'required|string',
            'deadline'    => 'required|date',

            
        ]);

DB::beginTransaction();

try {

    $oldCriteria = $scholarship->eligibilityCriteria;

    $scholarship->update([
        'title' => $request->title,
        'provider' => $request->provider,
        'description' => $request->description,
        'raw_eligibility' => $request->raw_eligibility,
        'deadline' => $request->deadline,
        'application_link'=> $request->application_link,
        'is_active' => $request->boolean('is_active'),
    ]);

    $scholarship->eligibilityCriteria()->updateOrCreate(
        ['scholarship_id' => $scholarship->id],
        $this->getEligibilityCriteriaData($request, $oldCriteria) // ✅ FIX
    );

    DB::commit();

    return redirect()
        ->route('admin.scholarships.index')
        ->with('success', 'Scholarship updated successfully');

} catch (\Exception $e) {
    DB::rollBack();

    return back()
        ->withInput()
        ->with('error', $e->getMessage());
}

    }

 protected function getEligibilityCriteriaData(
    Request $request,
    $oldCriteria
) {
    return [
        'min_spm_as' => $request->input('min_spm_as', $oldCriteria->min_spm_as ?? null),

        'academic_categories' => $request->filled('academic_categories')
            ? $request->input('academic_categories')
            : ($oldCriteria->academic_categories ?? []),

        'income_categories' => $request->filled('income_categories')
            ? $request->input('income_categories')
            : ($oldCriteria->income_categories ?? []),

        'study_paths' => $request->filled('study_paths')
            ? $request->input('study_paths')
            : ($oldCriteria->study_paths ?? []),

        'min_age' => $request->input('min_age', $oldCriteria->min_age ?? null),
        'max_age' => $request->input('max_age', $oldCriteria->max_age ?? null),

        'bumiputera_required' => $request->boolean(
            'bumiputera_required',
            $oldCriteria->bumiputera_required ?? false
        ),

        'bumiputera_priority' => $request->boolean(
            'bumiputera_priority',
            $oldCriteria->bumiputera_priority ?? false
        ),

        'match_all_criteria' => true,
        'priority_weight' => 1,
    ];
}




    
    /**
     * Remove the specified scholarship from storage.
     */
public function destroy($id)
{
    try {
        $scholarship = Scholarship::findOrFail($id);

        // ONLY check bookmarks (this exists)
        $bookmarksCount = $scholarship->bookmarks()->count();

        $scholarship->delete();

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', 'Scholarship deleted successfully!');

    } catch (\Exception $e) {
        return redirect()
            ->route('admin.scholarships.index')
            ->with('error', 'Failed to delete scholarship: ' . $e->getMessage());
    }
}

    
    /**
     * Toggle the active status of a scholarship.
     */
    public function toggleStatus($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        $scholarship->update(['is_active' => !$scholarship->is_active]);
        
        $status = $scholarship->is_active ? 'activated' : 'deactivated';
        
        // FIX: Change from scholarships.show to admin.scholarships.show
        return redirect()->route('admin.scholarships.show', $scholarship)
            ->with('success', 'Scholarship ' . $status . ' successfully!');
    }
    
    /**
     * Display all scholarships for public view.
     */
    public function browse()
    {
        $scholarships = Scholarship::active()
            ->upcoming()
            ->latest()
            ->paginate(12);
        
        return view('scholarships.browse', compact('scholarships'));
    }
    
    /**
     * Search scholarships.
     */
    public function search(Request $request)
    {
        $query = Scholarship::active()->upcoming();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('academic_category')) {
            $query->where('academic_category', $request->get('academic_category'));
        }
        
        if ($request->has('income_category')) {
            $query->where('income_category', $request->get('income_category'));
        }
        
        if ($request->has('study_path')) {
            $query->where('study_path', $request->get('study_path'));
        }
        
        $scholarships = $query->latest()->paginate(12);
        
        return view('scholarships.search', compact('scholarships'));
    }

    public function showPublic($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        return view('scholarship.show', compact('scholarship'));
    }


    // ADMIN-facing show
    public function showAdmin($id)
    {
        $scholarship = Scholarship::withCount(['bookmarks'])
            ->findOrFail($id);

        return view('admin.scholarships.show', compact('scholarship'));
    }

    
}