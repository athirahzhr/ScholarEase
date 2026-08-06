<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Scholarship;
use App\Models\ScholarshipEligibilityCriteria;
use App\Models\Bookmark;
use App\Models\ScrapingLog;
use App\Models\Feedback;  
use App\Notifications\ScholarshipDeadlineNear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
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
            'b40_friendly' => ScholarshipEligibilityCriteria::whereNotNull('max_monthly_income')->count(),
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
     * Show test matching form
     */
    public function showTestForm()
    {
        return view('admin.eligibility.test-form');
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
            'ID',
            'Title',
            'Provider',
            'Min As',
            'Max Monthly Income',
            'Study Levels',
            'Fields Of Study',
            'Bumiputera Required',
            'Max Age',
            'Bond Required',
            'Has Criteria'
        ];

        foreach ($scholarships as $scholarship) {

            $criteria = $scholarship->eligibilityCriteria;

            $csv[] = [
                $scholarship->id,

                $scholarship->title,

                $scholarship->provider,

                $criteria->min_spm_as ?? 'N/A',

                $criteria->max_monthly_income ?? 'N/A',

                $criteria && $criteria->study_paths
                    ? json_encode($criteria->study_paths)
                    : 'N/A',

                $criteria && $criteria->fields_of_study
                    ? json_encode($criteria->fields_of_study)
                    : 'N/A',

                $criteria && $criteria->bumiputera_required
                    ? 'Yes'
                    : 'No',

                $criteria->max_age ?? 'N/A',

                $criteria && $criteria->bond_required
                    ? 'Yes'
                    : 'No',

                $criteria ? 'Yes' : 'No',
            ];
        }

        $filename =
            'scholarships_eligibility_' .
            date('Y-m-d') .
            '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' =>
                "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($csv) {

            $file = fopen('php://output', 'w');

            foreach ($csv as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }


    public function sendDeadlineNotification()
    {
        Artisan::call('notify:scholarship-deadline');

        return back()->with('success', 'Deadline notifications sent successfully!');
    }
    public function notifications()
    {
        $totalBookmarks = Bookmark::count();

        $pending = Bookmark::where('notification_status', 'pending')->count();
        $sent = Bookmark::where('notification_status', 'success')->count();
        $failed = Bookmark::where('notification_status', 'failed')->count();

        $pendingList = Bookmark::with(['user', 'scholarship'])
            ->where('notification_status', 'pending')
            ->latest()
            ->get();

        $history = DB::table('notifications')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.notifications.index', compact(
            'totalBookmarks',
            'pending',
            'sent',
            'failed',
            'pendingList',
            'history'
        ));
    }

    public function notifySingle(Request $request)
    {
        $bookmark = Bookmark::with(['user', 'scholarship'])
            ->findOrFail($request->bookmark_id);

        if (!$bookmark->user || !$bookmark->scholarship) {
            return back()->with('error', 'Invalid data');
        }

        $daysLeft = now()->diffInDays($bookmark->scholarship->deadline, false);

    try {
        $bookmark->user->notify(
            new ScholarshipDeadlineNear(
                $bookmark->scholarship,
                $daysLeft
            )
        );

        $bookmark->update([
            'notified_at' => now(),
            'notification_status' => 'success',
            'notification_error' => null
        ]);

        return back()->with('success', 'Notification sent successfully!');

    } catch (\Exception $e) {

        $bookmark->update([
            'notification_status' => 'failed',
            'notification_error' => $e->getMessage()
        ]);

        return back()->with('error', 'Failed: ' . $e->getMessage());
    }
    }
    
    public function feedbacks()
    {
        $feedbacks = Feedback::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.feedbacks', compact('feedbacks'));
    }

    
    /**
     * Reject/Delete a feedback
     */
    public function deleteFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();
        
        return redirect()->back()->with('success', 'Feedback deleted successfully.');
    }
}

