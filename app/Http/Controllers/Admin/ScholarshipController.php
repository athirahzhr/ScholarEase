<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;
use App\Services\ScholarshipRuleMatcher;
use App\Services\ScholarshipInferenceEngine;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    /**
     * Store a newly created scholarship with new eligibility system
     */
public function store(Request $request)
{
    $request->validate([
        'title'       => 'required|string',
        'provider'    => 'required|string',
        'description' => 'required|string',
        'raw_eligibility' => 'nullable|string',
        'deadline'    => 'nullable|date',
    ]);

    DB::beginTransaction();

    try {
        // 1️⃣ Save scholarship (ONLY existing columns)
        $scholarship = Scholarship::create([
            'title'            => $request->title,
            'provider'         => $request->provider,
            'description'      => $request->description,
            'raw_eligibility'  => $request->raw_eligibility,
            'application_link' => $request->application_link,
            'deadline'         => $request->deadline,
            'source'           => 'manual',
            'is_active'        => true,
        ]);

        // 2️⃣ Legacy auto categorization (optional)
        app(ScholarshipRuleMatcher::class)->categorizeSingle($scholarship);
        app(ScholarshipInferenceEngine::class)->inferSingle($scholarship);

        // 3️⃣ Eligibility Criteria
        $this->createEligibilityCriteria($scholarship, $request);

        DB::commit();

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', 'Scholarship created successfully');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}


    /**
     * Create eligibility criteria for the scholarship
     */
  protected function createEligibilityCriteria(Scholarship $scholarship, Request $request)
{
    ScholarshipEligibilityCriteria::create([
        'scholarship_id' => $scholarship->id,

        // Academic
        'min_spm_as' => $request->min_spm_as,
        'academic_categories' => $request->input('academic_categories'),

        // Financial
        'income_categories' => $request->input('income_categories'),
        'max_monthly_income' => $request->max_monthly_income,

        // Study
        'study_paths' => $request->input('study_paths'),
        'fields_of_study' => $request->input('fields_of_study'),

        // Demographic
        'gender_requirement' => $request->gender_requirement ?? 'Any',
        'citizenship_required' => $request->citizenship_required ?? 'Malaysian',

        // Logic
        'match_all_criteria' => true,
        'priority_weight' => 1,
        'notes' => $request->eligibility_notes,
    ]);
}

    /**
     * Update scholarship and eligibility criteria
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
            // Update scholarship
           $scholarship->update([
                'title'            => $request->title,
                'provider'         => $request->provider,
                'description'      => $request->description,
                'raw_eligibility'  => $request->raw_eligibility,
                'deadline'         => $request->deadline,
                'application_link'=> $request->application_link,
                'is_active'        => $request->boolean('is_active'),
            ]);



            // Update or create eligibility criteria
            $scholarship->eligibilityCriteria()->updateOrCreate(
            ['scholarship_id' => $scholarship->id],
            $this->getEligibilityCriteriaData($request)
            );

            DB::commit();

            return redirect()
                ->route('admin.scholarships.index')
                ->with('success', 'Scholarship updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update scholarship: ' . $e->getMessage());
        }
    }

    /**
     * Helper to prepare eligibility criteria data
     */
 protected function getEligibilityCriteriaData(Request $request)
{
    return [
        'min_spm_as' => $request->min_spm_as,
        'academic_categories' => $request->input('academic_categories'),
        'required_subjects' => $request->input('required_subjects'),

        'income_categories' => $request->input('income_categories'),
        'max_monthly_income' => $request->max_monthly_income,

        'study_paths' => $request->input('study_paths'),
        'fields_of_study' => $request->input('fields_of_study'),

        'bumiputera_required' => $request->boolean('bumiputera_required'),
        'bumiputera_priority' => $request->boolean('bumiputera_priority'),
        'gender_requirement' => $request->gender_requirement ?? 'Any',
        'citizenship_required' => $request->citizenship_required ?? 'Malaysian',
        'state_requirement' => $request->state_requirement,
        'rural_priority' => $request->boolean('rural_priority'),

        'min_age' => $request->min_age,
        'max_age' => $request->max_age,

        'leadership_required' => $request->boolean('leadership_required'),
        'leadership_priority' => $request->boolean('leadership_priority'),
        'sports_achievement' => $request->boolean('sports_achievement'),
        'min_community_hours' => $request->min_community_hours,

        'bond_required' => $request->boolean('bond_required'),
        'bond_years' => $request->bond_years,

        'match_all_criteria' => true,
        'priority_weight' => $request->priority_weight ?? 1,
        'notes' => $request->eligibility_notes,
    ];



    }

    /**
     * Toggle scholarship active status
     */
    public function toggleStatus(Scholarship $scholarship)
    {
        $scholarship->update([
            'is_active' => !$scholarship->is_active,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Scholarship ' . ($scholarship->is_active ? 'activated' : 'deactivated') . ' successfully.'
            );
    }

    /**
     * Find matching scholarships for a student profile using stored procedure
     */
    public function findMatches(Request $request)
    {
        $request->validate([
            'total_as' => 'required|integer|min:0|max:12',
            'income_category' => 'required|in:B1,B3,B4',
            'study_path' => 'required|in:C1,C2,C3,C4',
            'bumiputera' => 'required|boolean',
            'age' => 'required|integer|min:15|max:30',
            'gender' => 'required|in:Male,Female',
            'state' => 'required|string',
            'has_leadership' => 'required|boolean',
        ]);

        try {
            // Call the stored procedure
            $matches = DB::select('CALL find_matching_scholarships(?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->total_as,
                $request->income_category,
                $request->study_path,
                $request->bumiputera,
                $request->age,
                $request->gender,
                $request->state,
                $request->has_leadership,
            ]);

            return response()->json([
                'success' => true,
                'count' => count($matches),
                'scholarships' => $matches,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to find matches: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get scholarship details with eligibility criteria
     */
    public function show(Scholarship $scholarship)
    {
        $scholarship->load('eligibilityCriteria');
        
        return view('admin.scholarships.show', compact('scholarship'));
    }

    /**
     * Bulk update eligibility criteria for scraped scholarships
     */
    public function bulkUpdateEligibility()
{
    return back()->with(
        'info',
        'Legacy bulk update disabled. Eligibility is now managed via rule-based system.'
    );
}
}