@extends('layouts.app')

@section('title', 'Dashboard - ScholarEase')

@section('content')
<style>
    :root {
        --maroon: #7A0019;
        --maroon-dark: #4e0010;
        --maroon-light: #9e1e32;
        --gold: #F4C542;
        --gold-light: #ffda77;
        --cream: #FFF8EE;
        --cream-dark: #f5ebe0;
        --gray-800: #1f2937;
        --gray-600: #4b5563;
        --success: #10b981;
        --warning: #f59e0b;
        --gradient-primary: linear-gradient(135deg, #7A0019 0%, #4e0010 100%);
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-header {
        background: var(--gradient-primary);
        color: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow);
        border-bottom: 3px solid var(--gold);
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border-top: 4px solid var(--gold);
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-icon {
        width: 55px;
        height: 55px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(244,197,66,0.1));
        color: var(--maroon);
    }
    
    .stat-card h5 {
        color: var(--maroon);
        font-weight: 700;
    }
    
    .badge-category {
        background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.08));
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .sidebar-nav {
        background: white;
        border-radius: 20px;
        padding: 1rem;
        box-shadow: var(--shadow);
    }
    
    .nav-item {
        padding: 0.75rem 1rem;
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        color: var(--gray-600);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .nav-item:hover, .nav-item.active {
        background: linear-gradient(135deg, rgba(122,0,25,0.08), rgba(244,197,66,0.08));
        color: var(--maroon);
    }
    
    .nav-item i {
        width: 22px;
        text-align: center;
    }
    
    .greeting-text {
        font-size: 1rem;
        opacity: 0.9;
    }
    
    .time-date-widget {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        backdrop-filter: blur(10px);
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: none;
        border-left: 4px solid #f59e0b;
        border-radius: 16px;
        color: #92400e;
    }
    
    .btn-primary {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        border: none;
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }
    
    .btn-outline-primary {
        border: 2px solid var(--maroon);
        color: var(--maroon);
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: transparent;
    }
    
    .btn-outline-primary:hover {
        background: var(--maroon);
        color: white;
        transform: translateY(-2px);
    }
    
    .scholarship-card {
        background: white;
        border-radius: 20px;
        padding: 1.25rem;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .scholarship-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--maroon));
    }
    
    .scholarship-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: var(--gold);
    }
    
    .guidance-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border-left: 4px solid var(--gold);
        box-shadow: var(--shadow);
    }
    
    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 1.2rem;
    }
    
    .step-icon.upload { background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(122,0,25,0.05)); color: var(--maroon); }
    .step-icon.match { background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.08)); color: #92400e; }
    .step-icon.view { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .step-icon.bookmark { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
    
    .progress-bar-custom {
        background: linear-gradient(90deg, var(--maroon), var(--gold));
    }

    /* ============================================ */
    /* FEEDBACK SECTION STYLES                      */
    /* ============================================ */
    .feedback-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border-left: 4px solid var(--gold);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    .feedback-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .feedback-rating {
        display: flex;
        gap: 2px;
        color: var(--gold);
        font-size: 1rem;
    }

    .feedback-rating .star-filled {
        color: var(--gold);
    }

    .feedback-rating .star-empty {
        color: #d1d5db;
    }

    .feedback-meta {
        font-size: 0.8rem;
        color: var(--gray-600);
    }

    .feedback-meta i {
        color: var(--maroon);
        margin-right: 4px;
    }

    .feedback-empty {
        text-align: center;
        padding: 3rem 1rem;
        background: #f9fafb;
        border-radius: 16px;
        border: 2px dashed #e5e7eb;
    }

    .feedback-empty i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
        display: block;
    }

    .feedback-empty h6 {
        color: var(--gray-800);
        margin-bottom: 0.5rem;
    }

    .feedback-empty p {
        color: var(--gray-600);
        margin-bottom: 1rem;
    }

    .feedback-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        color: white;
        flex-shrink: 0;
    }

    .feedback-avatar.bg-maroon { background: var(--maroon); }
    .feedback-avatar.bg-gold { background: var(--gold); color: #2c1a00; }
    .feedback-avatar.bg-green { background: #10b981; }
    .feedback-avatar.bg-blue { background: #3b82f6; }
    .feedback-avatar.bg-purple { background: #8b5cf6; }
    .feedback-avatar.bg-orange { background: #f59e0b; }

    .feedback-content {
        flex: 1;
    }

    .feedback-content h6 {
        color: var(--gray-800);
        margin-bottom: 2px;
        font-weight: 600;
    }

    .feedback-content p {
        color: var(--gray-600);
        margin-bottom: 4px;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .feedback-content .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .feedback-status-badge {
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    .feedback-status-badge.approved {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .feedback-status-badge.pending {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
    }

    .feedback-status-badge.rejected {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .greeting-text {
            font-size: 0.9rem;
        }

        .feedback-card {
            padding: 1rem;
        }

        .feedback-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .feedback-content h6 {
            font-size: 0.9rem;
        }

        .feedback-content p {
            font-size: 0.8rem;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header" data-aos="fade-down">
        <div class="row align-items-center">
            <div class="col-md-7 mb-3 mb-md-0">
                <h1 class="h2 mb-2">Welcome, {{ auth()->user()->name }}! 👋</h1>
                <p class="greeting-text mb-0">Here's what's happening with your scholarship journey today.</p>
            </div>
            <div class="col-md-5">
                <div class="time-date-widget">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <span><i class="fas fa-sun me-2"></i> 24°C</span>
                        <span class="px-2">•</span>
                        <span>Partly Cloudy</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center gap-3" id="datetime-widget">
                        <span><i class="fas fa-clock me-2"></i> <span id="currentTime">--:--</span></span>
                        <span class="px-2">•</span>
                        <span><i class="fas fa-calendar me-2"></i> <span id="currentDate">--/--/----</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="sidebar-nav sticky-top" style="top: 20px;">
                <h5 class="mb-3 px-2" style="color: var(--maroon);">Navigation</h5>
                <a href="{{ route('dashboard') }}" class="nav-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('scholarship.finder') }}" class="nav-item">
                    <i class="fas fa-search"></i>
                    <span>Find Scholarship</span>
                </a>
                <a href="{{ route('bookmarks.index') }}" class="nav-item">
                    <i class="fas fa-bookmark"></i>
                    <span>Bookmarks</span>
                    @if(auth()->user()->bookmarks()->count() > 0)
                        <span class="badge ms-auto" style="background: var(--maroon); color: white;">{{ auth()->user()->bookmarks()->count() }}</span>
                    @endif
                </a>
                <a href="{{ route('scholarship.recommendations') }}" class="nav-item">
                    <i class="fas fa-star"></i>
                    <span>Recommendations</span>
                </a>
                <a href="{{ route('feedback.create') }}" class="nav-item">
                    <i class="fas fa-star me-2" style="color: var(--gold);"></i>
                    <span>Give Feedback</span>
                </a>
                @if(auth()->user()->isAdmin())
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted px-2">Admin Panel</small>
                        <a href="{{ route('admin.dashboard') }}" class="nav-item">
                            <i class="fas fa-crown"></i>
                            <span>Admin Dashboard</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="mb-1">Your Profile</h5>
                        @if(auth()->user()->profile)
                            <p class="text-success mb-2">Complete ✓</p>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge-category">{{ auth()->user()->profile->academic_category ?? 'N/A' }}</span>
                                <span class="badge-category">{{ auth()->user()->profile->income_category ?? 'N/A' }}</span>
                                <span class="badge-category">{{ auth()->user()->profile->study_level ?? 'N/A' }}</span>
                            </div>
                        @else
                            <p class="text-muted mb-3">Incomplete</p>
                            <a href="{{ route('scholarship.finder') }}" class="btn btn-primary btn-sm">Complete Profile</a>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <h5 class="mb-1">Bookmarks</h5>
                        <p class="display-6 fw-bold mb-2" style="color: var(--maroon);">{{ auth()->user()->bookmarks()->count() }}</p>
                        <p class="text-muted mb-0">Saved Scholarships</p>
                        <a href="{{ route('bookmarks.index') }}" class="btn btn-outline-primary btn-sm mt-2">View All</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h5 class="mb-1">Recommendations</h5>
                        @if(auth()->user()->profile)
                            <p class="display-6 fw-bold mb-2" style="color: var(--maroon);">{{ $recommendationCount ?? 0 }}</p>
                            <p class="text-muted mb-0">Matching Scholarships</p>
                            <a href="{{ route('scholarship.recommendations') }}" class="btn btn-outline-primary btn-sm mt-2">View Matches</a>
                        @else
                            <p class="text-muted mb-3">Complete profile to see matches</p>
                            <a href="{{ route('scholarship.finder') }}" class="btn btn-primary btn-sm">Get Started</a>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="mb-1">Scholarship Status</h5>
                        <p class="display-6 fw-bold mb-2" style="color: var(--maroon);">{{ \App\Models\Scholarship::count() }}</p>
                        <p class="text-muted mb-0">Total Available</p>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar" style="width: 85%; background: linear-gradient(90deg, var(--maroon), var(--gold));"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card" style="background: linear-gradient(135deg, #FFF8EE, #f5ebe0); border-left: 4px solid var(--gold);">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="mb-1" style="color: var(--maroon);">
                                    <i class="fas fa-star me-2" style="color: var(--gold);"></i>
                                    Share Your Experience
                                </h5>
                                <p class="text-muted mb-0">Your feedback helps us improve ScholarEase and serve you better!</p>
                            </div>
                            <a href="{{ route('feedback.create') }}" class="btn btn-primary">
                                <i class="fas fa-comment me-2"></i> Give Feedback
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- USER FEEDBACK SECTION                        -->
            <!-- ============================================ -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 style="color: var(--maroon);">
                            <i class="fas fa-comments me-2"></i>
                            Recent Feedback
                        </h4>
                        <a href="{{ route('feedback.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Feedback
                        </a>
                    </div>

                    @php
                        $userFeedback = auth()->user()->feedback()->latest()->take(5)->get();
                    @endphp

                    @if($userFeedback->count() > 0)
                        <div class="row">
                            @foreach($userFeedback as $feedback)
                                <div class="col-12" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                                    <div class="feedback-card">
                                        <div class="d-flex gap-3">
                                            <!-- Avatar -->
                                            <div class="feedback-avatar bg-maroon">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                            
                                            <!-- Content -->
                                            <div class="feedback-content">
                                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                    <div>
                                                        <h6>{{ auth()->user()->name }}</h6>
                                                        <div class="feedback-rating">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= $feedback->rating)
                                                                    <i class="fas fa-star star-filled"></i>
                                                                @else
                                                                    <i class="fas fa-star star-empty"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="feedback-status-badge {{ $feedback->approved ? 'approved' : ($feedback->rejected ? 'rejected' : 'pending') }}">
                                                            {{ $feedback->approved ? 'Approved' : ($feedback->rejected ? 'Rejected' : 'Pending') }}
                                                        </span>
                                                        <div class="feedback-meta mt-1">
                                                            <i class="far fa-clock"></i>
                                                            {{ $feedback->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="mb-0">{{ Str::limit($feedback->comment, 150) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($userFeedback->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('feedback.history') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> View All Feedback
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="feedback-empty">
                            <i class="fas fa-comment-slash"></i>
                            <h6>No Feedback Yet</h6>
                            <p>You haven't submitted any feedback. Share your experience with us!</p>
                            <a href="{{ route('feedback.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i> Write Feedback
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Guideline -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="guidance-card" data-aos="fade-up">
                        <h4 class="mb-3">
                            <i class="fas fa-info-circle me-2" style="color: var(--maroon);"></i>
                            How to Use ScholarEase
                        </h4>
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <div class="step-icon upload mx-auto">
                                    <i class="fas fa-upload fa-lg"></i>
                                </div>
                                <h6>Step 1</h6>
                                <p class="text-muted small">Upload SPM result / fill profile</p>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <div class="step-icon match mx-auto">
                                    <i class="fas fa-cogs fa-lg"></i>
                                </div>
                                <h6>Step 2</h6>
                                <p class="text-muted small">System auto-match scholarships</p>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <div class="step-icon view mx-auto">
                                    <i class="fas fa-star fa-lg"></i>
                                </div>
                                <h6>Step 3</h6>
                                <p class="text-muted small">View recommended scholarships</p>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <div class="step-icon bookmark mx-auto">
                                    <i class="fas fa-bookmark fa-lg"></i>
                                </div>
                                <h6>Step 4</h6>
                                <p class="text-muted small">Bookmark & track deadlines</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured Scholarships -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 style="color: var(--maroon);">Featured Scholarships</h4>
                        <a href="{{ route('scholarship.recommendations') }}" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    
                    <div class="row">
                        @php
                            $featuredScholarships = \App\Models\Scholarship::where('deadline', '>', now())
                                ->inRandomOrder()
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        @forelse($featuredScholarships as $scholarship)
                        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                            <div class="scholarship-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge" style="background: var(--maroon); color: white;">{{ $scholarship->provider }}</span>
                                    @if($scholarship->deadline && $scholarship->deadline->diffInDays(now()) < 7)
                                        <span class="badge" style="background: #f59e0b; color: white;">Closing Soon</span>
                                    @endif
                                </div>
                                <h6 class="mb-2" style="color: var(--maroon);">{{ Str::limit($scholarship->title, 40) }}</h6>
                                <p class="text-muted small mb-3">{{ Str::limit($scholarship->description ?? 'No description available', 80) }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        @if($scholarship->amount)
                                            <span class="fw-bold" style="color: #10b981;">RM {{ number_format($scholarship->amount) }}</span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Deadline:</small>
                                        <small class="fw-bold">{{ $scholarship->deadline ? $scholarship->deadline->format('d M Y') : 'Rolling' }}</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    @if($scholarship->application_link)
                                        <a href="{{ $scholarship->application_link }}" target="_blank" class="btn btn-primary btn-sm flex-grow-1">Apply</a>
                                    @endif
                                    <form action="{{ route('bookmarks.toggle', $scholarship->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-12">
                                <div class="alert-info text-center py-4" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 16px;">
                                    <i class="fas fa-info-circle fa-2x mb-2" style="display:block; color: #1e40af;"></i>
                                    <p class="mb-0" style="color: #1e40af;">No featured scholarships available at the moment. Check back later!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    // Update time and date dynamically
    function updateDateTime() {
        const now = new Date();
        const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        
        const timeElement = document.getElementById('currentTime');
        const dateElement = document.getElementById('currentDate');
        
        if(timeElement) timeElement.textContent = timeStr;
        if(dateElement) dateElement.textContent = dateStr;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 60000);
</script>
@endpush
@endsection