<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;
use App\Models\Bookmark;
use App\Models\ScrapingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\ScholarshipRuleMatcher;

class AdminController extends Controller
{
    protected $matcher;
    
    public function __construct(ScholarshipRuleMatcher $matcher)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->matcher = $matcher;
    }
    
    /**
     * Display admin dashboard.
     */
    public function dashboard()
{
    $stats = [
        'totalUsers' => User::count(),
        'totalScholarships' => Scholarship::count(),
        'totalBookmarks' => Bookmark::count(),

        // Replaced Applications (system has no application module)
        'totalEligibilityRules' => ScholarshipEligibilityCriteria::count(),

        'recentUsers' => User::latest()->take(5)->get(),
        'recentScholarships' => Scholarship::latest()->take(5)->get(),

        // Scraping status
        'latestScrape' => ScrapingLog::latest()->first(),
        'recentScrapingLogs' => ScrapingLog::latest()->take(5)->get(),

        // Eligibility coverage
        'scholarshipsWithEligibility' => ScholarshipEligibilityCriteria::count(),
        'scholarshipsWithoutEligibility' => Scholarship::doesntHave('eligibilityCriteria')
            ->where('is_active', true)
            ->count(),

        // Eligibility insights
        'eligibilityStats' => $this->getEligibilityStats(),
    ];

    return view('admin.dashboard', $stats);
}

    
    /**
     * NEW: Get eligibility criteria statistics
     */
    protected function getEligibilityStats()
    {
        return [
            'total_with_criteria' => ScholarshipEligibilityCriteria::count(),
            'require_bumiputera' => ScholarshipEligibilityCriteria::where('bumiputera_required', true)->count(),
            'require_leadership' => ScholarshipEligibilityCriteria::where('leadership_required', true)->count(),
            'with_bond' => ScholarshipEligibilityCriteria::where('bond_required', true)->count(),
            'b40_friendly' => ScholarshipEligibilityCriteria::whereJsonContains('income_categories', 'B1')->count(),
            'overseas_only' => ScholarshipEligibilityCriteria::where('study_destination', 'Overseas')->count(),
        ];
    }
    
    // ============================================
    // USER MANAGEMENT METHODS
    // ============================================
    
    public function index()
    {
        $users = User::with('profile')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }
    
    public function show($id)
    {
        $user = User::with(['profile', 'bookmarks'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
            'is_active' => 'boolean',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            
        ]);
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'You cannot delete your own account.');
        }
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Cannot delete admin users.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
    
    public function create()
    {
        return view('admin.users.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }
    
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own status.');
        }
        
        $user->update([
            'is_active' => !$user->is_active,
        ]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }
    
    
    // ============================================
    // NEW: ELIGIBILITY MANAGEMENT METHODS
    // ============================================
    
    /**
     * Bulk create eligibility criteria for scholarships missing them
     */
    public function bulkCreateEligibility()
    {
        try {
            $scholarships = Scholarship::doesntHave('eligibilityCriteria')
                ->where('is_active', true)
                ->get();

            $created = 0;

            foreach ($scholarships as $scholarship) {
                // Auto-create basic eligibility from legacy fields
                if ($scholarship->academic_category || $scholarship->income_category || $scholarship->study_path) {
                    
                    $minAs = match($scholarship->academic_category) {
                        'A1' => 0,
                        'A2' => 4,
                        'A3' => 7,
                        'A4' => 10,
                        default => null,
                    };

                    ScholarshipEligibilityCriteria::create([
                        'scholarship_id' => $scholarship->id,
                        'min_spm_as' => $minAs,
                        'academic_categories' => $scholarship->academic_category 
                            ? json_encode([$scholarship->academic_category]) 
                            : null,
                        'income_categories' => $scholarship->income_category 
                            ? json_encode([$scholarship->income_category]) 
                            : null,
                        'study_paths' => $scholarship->study_path 
                            ? json_encode([$scholarship->study_path]) 
                            : null,
                        'notes' => 'Auto-migrated from legacy fields',
                    ]);

                    $created++;
                }
            }

            return back()->with('success', "Created eligibility criteria for {$created} scholarships!");
            
        } catch (\Exception $e) {
            Log::error('Bulk eligibility creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create eligibility criteria: ' . $e->getMessage());
        }
    }
    
    /**
     * View eligibility management dashboard
     */
    public function eligibilityDashboard()
    {
        $stats = [
            'total_scholarships' => Scholarship::active()->count(),
            'with_eligibility' => ScholarshipEligibilityCriteria::count(),
            'without_eligibility' => Scholarship::doesntHave('eligibilityCriteria')
                ->where('is_active', true)
                ->count(),
            'complete_eligibility' => ScholarshipEligibilityCriteria::whereNotNull('min_spm_as')
                ->whereNotNull('income_categories')
                ->whereNotNull('study_paths')
                ->count(),
        ];
        
        // Recent scholarships without criteria
        $needsEligibility = Scholarship::doesntHave('eligibilityCriteria')
            ->where('is_active', true)
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.eligibility.dashboard', compact('stats', 'needsEligibility'));
    }
    
    /**
     * Test matching system with sample student
     */
    public function testMatching(Request $request)
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
            // Call stored procedure
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

            $studentProfile = [
                'total_as' => $request->total_as,
                'income_category' => $request->income_category,
                'study_path' => $request->study_path,
                'bumiputera' => $request->bumiputera,
                'age' => $request->age,
                'gender' => $request->gender,
                'state' => $request->state,
                'has_leadership' => $request->has_leadership,
            ];

            return view('admin.eligibility.test-results', compact('matches', 'studentProfile'));
            
        } catch (\Exception $e) {
            Log::error('Test matching failed: ' . $e->getMessage());
            return back()->with('error', 'Test matching failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Show test matching form
     */
    public function showTestForm()
    {
        return view('admin.eligibility.test-form');
    }
    
    /**
     * Verify stored procedure exists
     */
    public function verifyStoredProcedure()
    {
        try {
            $result = DB::select("SHOW PROCEDURE STATUS WHERE Name = 'find_matching_scholarships' AND Db = DATABASE()");
            
            if (empty($result)) {
                return back()->with('error', 'Stored procedure not found! Please run the SQL script to create it.');
            }
            
            return back()->with('success', 'Stored procedure is installed and ready!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error checking stored procedure: ' . $e->getMessage());
        }
    }
    
    /**
     * Export eligibility data as CSV for review
     */
    public function exportEligibility()
    {
        $scholarships = Scholarship::with('eligibilityCriteria')
            ->where('is_active', true)
            ->get();
        
        $csv = [];
        $csv[] = [
            'ID', 'Title', 'Provider', 'Min As', 'Income Categories', 
            'Study Paths', 'Bumiputera Required', 'Max Age', 'Bond Required',
            'Has Criteria'
        ];
        
        foreach ($scholarships as $scholarship) {
            $criteria = $scholarship->eligibilityCriteria;
            
            $csv[] = [
                $scholarship->id,
                $scholarship->title,
                $scholarship->provider,
                $criteria->min_spm_as ?? 'N/A',
                $criteria ? json_encode($criteria->income_categories) : 'N/A',
                $criteria ? json_encode($criteria->study_paths) : 'N/A',
                $criteria && $criteria->bumiputera_required ? 'Yes' : 'No',
                $criteria->max_age ?? 'N/A',
                $criteria && $criteria->bond_required ? 'Yes' : 'No',
                $criteria ? 'Yes' : 'No',
            ];
        }
        
        $filename = 'scholarships_eligibility_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($csv) {
            $file = fopen('php://output', 'w');
            foreach ($csv as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}