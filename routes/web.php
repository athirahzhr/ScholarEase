<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ScrapingController;
use App\Http\Controllers\OCRController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminScraperController;
use App\Http\Controllers\ProfileController;
use App\Models\Scholarship;
use App\Models\Feedback;
use App\Http\Controllers\FeedbackController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Welcome Page - FIXED: Properly fetch scholarships and feedbacks
Route::get('/', function () {
    $today = \Carbon\Carbon::today();
    
    // Get active scholarships (not expired)
    $scholarships = Scholarship::where('is_active', 1)
        ->where(function($query) use ($today) {
            $query->whereNull('deadline')
                  ->orWhere('deadline', '>=', $today);
        })
        ->latest()
        ->take(10)
        ->get();
    
    // Get approved feedbacks for testimonials
    $feedbacks = Feedback::with('user')
        ->where('approved', 1)
        ->latest()
        ->take(6)
        ->get();
    
    return view('welcome', compact('scholarships', 'feedbacks'));
})->name('welcome');

// Authentication Routes
Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| USER ROUTES (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');

    // Scholarship Finder
    Route::get('/find-scholarship', [ScholarshipController::class, 'showFinder'])->name('scholarship.finder');
    Route::post('/save-profile', [ProfileController::class, 'store'])->name('save.profile');
    Route::get('/recommendations', [ScholarshipController::class, 'getRecommendations'])->name('scholarship.recommendations');

    // OCR Routes
    Route::post('/upload-spm', [OCRController::class, 'uploadSPM'])->name('upload.spm');
    Route::post('/update-ocr-results', [OCRController::class, 'updateOCRResults'])->name('update.ocr.results');
    Route::post('/verify-ocr-results', [OCRController::class, 'verifyOCRResults'])->name('verify.ocr.results');
    Route::post('/add-ocr-subject', [OCRController::class, 'addSubject'])->name('add.ocr.subject');
    Route::post('/remove-ocr-subject', [OCRController::class, 'removeSubject'])->name('remove.ocr.subject');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/toggle/{id}', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // Public scholarship browsing
    Route::get('/scholarships', [ScholarshipController::class, 'browse'])->name('scholarships.browse');
    Route::get('/scholarships/search', [ScholarshipController::class, 'search'])->name('scholarships.search');

    // User scholarship show
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'showPublic'])->name('scholarships.show');

    // Feedback Routes
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/', [AdminController::class, 'store'])->name('store');
        Route::get('/{user}', [AdminController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Scholarships Management
    Route::prefix('scholarships')->name('scholarships.')->group(function () {
        Route::get('/', [ScholarshipController::class, 'index'])->name('index');
        Route::get('/create', [ScholarshipController::class, 'create'])->name('create');
        Route::post('/', [ScholarshipController::class, 'store'])->name('store');
        Route::get('/{id}', [ScholarshipController::class, 'showAdmin'])->name('show');
        Route::get('/{id}/edit', [ScholarshipController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ScholarshipController::class, 'update'])->name('update');
        Route::delete('/{id}', [ScholarshipController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [ScholarshipController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Eligibility Management
    Route::prefix('eligibility')->name('eligibility.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'eligibilityDashboard'])->name('dashboard');
        Route::post('/bulk-create', [AdminController::class, 'bulkCreateEligibility'])->name('bulkCreate');
        Route::get('/test', [AdminController::class, 'showTestForm'])->name('test');
        Route::post('/test', [AdminController::class, 'testMatching'])->name('testMatching');
        Route::get('/verify-procedure', [AdminController::class, 'verifyStoredProcedure'])->name('verifyProcedure');
        Route::get('/export', [AdminController::class, 'exportEligibility'])->name('export');
    });

    // Scraping Management
    Route::prefix('scraping')->name('scraping.')->group(function () {
        Route::get('/logs', [ScrapingController::class, 'logs'])->name('logs');
    });

    // Scraper Controller
    Route::get('/scraper', [AdminScraperController::class, 'index'])->name('scraper.index');
    Route::post('/scraper/run', [AdminScraperController::class, 'run'])->name('scraper.run');
    Route::get('/scraper/review', [AdminScraperController::class, 'review'])->name('scraper.review');
    Route::post('/scraper/import', [AdminScraperController::class, 'import'])->name('scraper.import');

    // Scraping Logs
    Route::get('/scraping-logs', [ScrapingController::class, 'logs'])->name('scraping.logs');

    // Notifications
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/send-single', [AdminController::class, 'notifySingle'])->name('notifications.single');
    Route::post('/notifications/send-all', [AdminController::class, 'sendDeadlineNotification'])->name('notifications.all');
    Route::post('/notifications/deadline', [AdminController::class, 'sendDeadlineNotification'])->name('notify.deadline');

    // Feedback Management
    Route::get('/feedbacks', [AdminController::class, 'feedbacks'])->name('feedbacks');
    Route::post('/feedbacks/{id}/approve', [AdminController::class, 'approveFeedback'])->name('feedbacks.approve');
    Route::delete('/feedbacks/{id}/reject', [AdminController::class, 'rejectFeedback'])->name('feedbacks.reject');
    Route::post('/feedbacks/bulk-approve', [AdminController::class, 'bulkApproveFeedbacks'])->name('feedbacks.bulk-approve');
});