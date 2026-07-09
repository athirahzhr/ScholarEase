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
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
        color: white;
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
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    
    .btn-outline-primary:hover {
        background: var(--maroon);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-success {
        background: linear-gradient(115deg, #10b981, #059669);
        border: none;
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        background: linear-gradient(115deg, #059669, #047857);
        color: white;
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
        box-shadow: var(--shadow);
        border-left: 6px solid var(--gold);
        background: linear-gradient(135deg, #ffffff, #fffdf7);
    }
    
    .step-icon {
        width: 60px;
        height: 60px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 1.4rem;
        transition: all 0.3s ease;
    }
    
    .step-icon:hover {
        transform: scale(1.1);
    }
    
    .step-icon.complete { 
        background: linear-gradient(135deg, rgba(122,0,25,0.12), rgba(122,0,25,0.05)); 
        color: var(--maroon); 
    }
    
    .step-icon.find { 
        background: linear-gradient(135deg, rgba(244,197,66,0.2), rgba(244,197,66,0.08)); 
        color: #92400e; 
    }
    
    .step-icon.recommend { 
        background: linear-gradient(135deg, #d1fae5, #a7f3d0); 
        color: #065f46; 
    }
    
    .step-icon.bookmark { 
        background: linear-gradient(135deg, #dbeafe, #bfdbfe); 
        color: #1e40af; 
    }
    
    .step-number {
        display: inline-block;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--maroon);
        color: white;
        font-size: 0.8rem;
        font-weight: 700;
        line-height: 28px;
        text-align: center;
        margin-bottom: 0.5rem;
    }
    
    .guidance-card .step-item {
        padding: 1.25rem;
        border-radius: 16px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: 1px solid #f3f4f6;
        height: 100%;
    }
    
    .guidance-card .step-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border-color: var(--gold);
    }
    
    .progress-bar-custom {
        background: linear-gradient(90deg, var(--maroon), var(--gold));
    }
    
    /* Resource Centre Button */
    .btn-resource {
        background: linear-gradient(115deg, #dc2626, #b91c1c);
        border: none;
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    
    .btn-resource:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        color: white;
        background: linear-gradient(115deg, #b91c1c, #991b1b);
    }

    .welcome-banner {
        background: linear-gradient(135deg, rgba(122,0,25,0.05), rgba(244,197,66,0.08));
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        border: 1px solid rgba(244,197,66,0.2);
    }

    .profile-status-badge {
        padding: 0.4rem 1rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .profile-status-badge.complete {
        background: #d1fae5;
        color: #065f46;
    }
    
    .profile-status-badge.incomplete {
        background: #fef3c7;
        color: #92400e;
    }

    .arrow-connector {
        color: var(--gold);
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .step-description {
        min-height: 60px;
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
        
        .btn-resource {
            width: 100%;
            justify-content: center;
        }

        .arrow-connector {
            transform: rotate(90deg);
            padding: 0.25rem 0;
        }

        .guidance-card .step-item {
            margin-bottom: 0.5rem;
        }

        .step-description {
            min-height: auto;
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

    <!-- PROFILE STATUS BANNER -->
    <div class="welcome-banner mb-4" data-aos="fade-up">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <i class="fas fa-user-circle" style="font-size: 2.5rem; color: var(--maroon);"></i>
                    <div>
                        <h6 class="mb-1" style="color: var(--maroon);">Profile Status</h6>
                        @if(auth()->user()->profile)
                            <span class="profile-status-badge complete">
                                <i class="fas fa-check-circle me-1"></i> Complete
                            </span>
                            <small class="text-muted ms-2">
                                {{ auth()->user()->profile->academic_category ?? 'N/A' }} • 
                                {{ auth()->user()->profile->study_level ?? 'N/A' }}
                            </small>
                        @else
                            <span class="profile-status-badge incomplete">
                                <i class="fas fa-exclamation-circle me-1"></i> Incomplete
                            </span>
                            <small class="text-muted ms-2">Complete your profile to get personalized recommendations</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                @if(!auth()->user()->profile)
                    <a href="{{ route('scholarship.finder') }}" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Complete Profile
                    </a>
                @else
                    <a href="{{ route('scholarship.recommendations') }}" class="btn btn-primary">
                        <i class="fas fa-star me-1"></i> View Recommendations
                    </a>
                @endif
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
                <a href="{{ route('resource-centre') }}" class="nav-item">
                    <i class="fas fa-video me-2" style="color: #dc2626;"></i>
                    <span>Resource Centre</span>
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
            <!-- USER GUIDELINE - 3 Simple Steps -->
            <div class="guidance-card mb-4" data-aos="fade-up">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-route me-2" style="color: var(--maroon); font-size: 1.5rem;"></i>
                    <h4 class="mb-0" style="color: var(--maroon);">Your Scholarship Journey</h4>
                    <span class="ms-3 badge" style="background: var(--gold); color: var(--maroon); font-weight: 600;">3 Simple Steps</span>
                </div>
                <p class="text-muted mb-4">Follow these steps to find and track your perfect scholarship</p>
                
                <div class="row align-items-stretch">
                    <!-- Step 1: Complete Profile -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="step-item text-center h-100">
                            <div class="step-number mx-auto">1</div>
                            <div class="step-icon complete mx-auto">
                                <i class="fas fa-user-edit fa-lg"></i>
                            </div>
                            <h6 style="color: var(--maroon);">Complete Your Profile</h6>
                            <p class="text-muted small mb-3 step-description">Fill in your SPM results and academic details to help us find the best matches</p>
                            @if(auth()->user()->profile)
                                <span class="badge" style="background: #10b981; color: white; padding: 0.5rem 1rem;">
                                    <i class="fas fa-check me-1"></i> Done
                                </span>
                            @else
                                <a href="{{ route('scholarship.finder') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload me-1"></i> Complete Now
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Arrow Connector for Desktop -->
                    <div class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
                        <div class="arrow-connector">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    
                    <!-- Step 2: View Recommendations -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="step-item text-center h-100">
                            <div class="step-number mx-auto">2</div>
                            <div class="step-icon recommend mx-auto">
                                <i class="fas fa-star fa-lg"></i>
                            </div>
                            <h6 style="color: var(--maroon);">View Recommendations</h6>
                            <p class="text-muted small mb-3 step-description">See personalized scholarship suggestions tailored to your profile</p>
                            @if(auth()->user()->profile)
                                <a href="{{ route('scholarship.recommendations') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> View Matches
                                </a>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="fas fa-lock me-1"></i> Complete Profile First
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Arrow Connector for Desktop -->
                    <div class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
                        <div class="arrow-connector">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    
                    <!-- Step 3: Bookmark -->
                    <div class="col-md-3">
                        <div class="step-item text-center h-100">
                            <div class="step-number mx-auto">3</div>
                            <div class="step-icon bookmark mx-auto">
                                <i class="fas fa-bookmark fa-lg"></i>
                            </div>
                            <h6 style="color: var(--maroon);">Bookmark & Track</h6>
                            <p class="text-muted small mb-3 step-description">Save scholarships and track deadlines so you never miss an opportunity</p>
                            <a href="{{ route('bookmarks.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-bookmark me-1"></i> View Bookmarks
                                @if(auth()->user()->bookmarks()->count() > 0)
                                    <span class="badge ms-1" style="background: var(--maroon); color: white;">{{ auth()->user()->bookmarks()->count() }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
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

            <!-- Featured Scholarships -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h4 style="color: var(--maroon);">General Scholarships</h4>
                        <div class="d-flex gap-2 flex-wrap">
                        </div>
                    </div>
                    
                    <div class="row"> 
                        @foreach($featuredScholarships as $scholarship)
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
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Student Feedback -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h4 style="color: var(--maroon);">
                                <i class="fas fa-comments me-2"></i>
                                Student Feedback
                            </h4>
                            <small class="text-muted">
                                ⭐ {{ number_format($averageRating,1) }}/5
                                ({{ $totalFeedback }} reviews)
                            </small>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('feedback.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Share Feedback
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        @forelse($feedbacks as $feedback)
                        <div class="col-md-4 mb-4">
                            <div class="scholarship-card">
                                <div class="mb-2">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-muted fst-italic">
                                    "{{ Str::limit($feedback->comment,120) }}"
                                </p>
                                <hr>
                                <strong>{{ $feedback->user->name }}</strong>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-light">
                                No feedback available yet.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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