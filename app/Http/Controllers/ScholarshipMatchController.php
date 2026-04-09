<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;
use App\Services\ScholarshipRuleMatcher;

class ScholarshipMatchController extends Controller
{
    protected $matcher;
    
    public function __construct(ScholarshipRuleMatcher $matcher)
    {
        $this->matcher = $matcher;
    }
    
    public function showForm()
    {
        return view('scholarships.simple-match');
    }
    
    public function findMatches(Request $request)
    {
        $request->validate([
            'spm_grades' => 'required|integer|min:1|max:12',
            'income_category' => 'required|in:B40,M40,T20',
            'study_path' => 'required|in:Pre-University,Diploma,Matriculation,TVET',
        ]);
        
        $spmGrades = $request->spm_grades;
        $incomeCategory = $request->income_category;
        $studyPath = $request->study_path;
        
        // Get student's category using rule-based system
        $studentCategory = $this->matcher->getStudentCategory($spmGrades, $incomeCategory, $studyPath);
        
        if (!$studentCategory) {
            return back()->withErrors('Unable to determine scholarship category. Please check your inputs.');
        }
        
        // Get scholarships that match student's category
        $matchingScholarships = $this->matcher->getMatchingScholarships($spmGrades, $incomeCategory, $studyPath);
        
        // For debugging: also get all scholarships with their categories
        $allScholarships = Scholarship::where('is_active', true)
            ->whereDate('deadline', '>=', now())
            ->get()
            ->map(function ($scholarship) {
                $scholarship->calculated_category = $this->matcher->calculateScholarshipCategory($scholarship);
                return $scholarship;
            });
        
        return view('scholarships.match-results', [
            'studentCategory' => $studentCategory,
            'matchingScholarships' => $matchingScholarships,
            'allScholarships' => $allScholarships, // For debugging
            'spmGrades' => $spmGrades,
            'incomeCategory' => $incomeCategory,
            'studyPath' => $studyPath,
            'matchCount' => $matchingScholarships->count(),
        ]);
    }
}