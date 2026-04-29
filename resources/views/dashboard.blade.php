@extends('layouts.app')

@section('title', 'Dashboard - ScholarEase')

@section('content')
<style>
    :root {
        --primary-blue: #1e40af;
        --secondary-blue: #3b82f6;
        --light-blue: #dbeafe;
        --dark-gray: #374151;
        --medium-gray: #6b7280;
        --light-gray: #f9fafb;
        --white: #ffffff;
        --success: #10b981;
        --warning: #f59e0b;
        --gradient-primary: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .dashboard-header {
        background: var(--gradient-primary);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow);
    }
    
    .stat-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid var(--secondary-blue);
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .icon-graduation {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        color: white;
    }
    
    .icon-bookmark {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
    }
    
    .icon-profile {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: white;
    }
    
    .icon-recommend {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
    }
    
    .quick-action-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        height: 100%;
        border-top: 3px solid var(--secondary-blue);
    }
    
    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }
    
    .action-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--secondary-blue);
    }
    
    .weather-widget {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
    }
    
    .scholarship-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .scholarship-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        border-color: var(--secondary-blue);
    }
    
    .badge-category {
        background: var(--light-blue);
        color: var(--primary-blue);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .sidebar-nav {
        background: var(--white);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: var(--shadow);
    }
    
    .nav-item {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        color: var(--dark-gray);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .nav-item:hover, .nav-item.active {
        background: var(--light-blue);
        color: var(--primary-blue);
    }
    
    .nav-item i {
        width: 20px;
        text-align: center;
    }
    
    .greeting-text {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    .time-date-widget {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        backdrop-filter: blur(10px);
    }
</style>

<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2">Welcome, {{ auth()->user()->name }}! 👋</h1>
                <p class="greeting-text mb-0">Here's what's happening with your scholarship journey today.</p>
            </div>
            <div class="col-md-4">
                <div class="time-date-widget">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <span><i class="fas fa-sun me-2"></i> 24°C</span>
                        <span class="px-2">•</span>
                        <span>Partly Cloudy</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <span><i class="fas fa-clock me-2"></i> 6:38 AM</span>
                        <span class="px-2">•</span>
                        <span><i class="fas fa-calendar me-2"></i> 24/1/2026</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@if(auth()->user()->unreadNotifications->count())
    <div class="row mb-4">
        <div class="col-12">
            @foreach(auth()->user()->unreadNotifications->take(3) as $notification)
                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $notification->data['title'] }}</strong><br>
                        {{ $notification->data['message'] }}
                    </div>

                    @if(!empty($notification->data['action_url']))
                        <a href="{{ $notification->data['action_url'] }}"
                           target="_blank"
                           class="btn btn-sm btn-danger">
                            Apply Now
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif




    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="sidebar-nav sticky-top" style="top: 20px;">
                <h5 class="mb-3 px-2">Navigation</h5>
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
                        <span class="badge bg-primary ms-auto">{{ auth()->user()->bookmarks()->count() }}</span>
                    @endif
                </a>
                <a href="{{ route('scholarship.recommendations') }}" class="nav-item">
                    <i class="fas fa-star"></i>
                    <span>Recommendations</span>
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
                    <div class="stat-card">
                        <div class="stat-icon icon-graduation">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="mb-1">Your Profile</h5>
                        @if(auth()->user()->profile)
                            <p class="text-muted mb-2">Complete ✓</p>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge-category">{{ auth()->user()->profile->academic_category }}</span>
                                <span class="badge-category">{{ auth()->user()->profile->income_category }}</span>
                                <span class="badge-category">{{ auth()->user()->profile->study_path }}</span>
                            </div>
                        @else
                            <p class="text-muted mb-3">Incomplete</p>
                            <a href="{{ route('scholarship.finder') }}" class="btn btn-sm btn-primary">Complete Profile</a>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon icon-bookmark">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <h5 class="mb-1">Bookmarks</h5>
                        <p class="display-6 fw-bold mb-2">{{ auth()->user()->bookmarks()->count() }}</p>
                        <p class="text-muted mb-0">Saved Scholarships</p>
                        <a href="{{ route('bookmarks.index') }}" class="btn btn-sm btn-outline-primary mt-2">View All</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon icon-recommend">
                            <i class="fas fa-star"></i>
                        </div>
                        <h5 class="mb-1">Recommendations</h5>
                        @if(auth()->user()->profile)
                            <p class="display-6 fw-bold mb-2">{{ $recommendationCount }}</p>
                            <p class="text-muted mb-0">Matching Scholarships</p>
                            <a href="{{ route('scholarship.recommendations') }}" class="btn btn-sm btn-outline-primary mt-2">View Matches</a>
                        @else
                            <p class="text-muted mb-3">Complete profile to see matches</p>
                            <a href="{{ route('scholarship.finder') }}" class="btn btn-sm btn-primary">Get Started</a>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon icon-profile">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="mb-1">Scholarship Status</h5>
                        <p class="display-6 fw-bold mb-2">{{ \App\Models\Scholarship::count() }}</p>
                        <p class="text-muted mb-0">Total Available</p>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- User Guideline -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card p-4 shadow-sm" style="border-left: 5px solid #3b82f6;">
                    
                    <h4 class="mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        How to Use ScholarEase
                    </h4>

                    <div class="row">

                        <div class="col-md-3 text-center mb-3">
                            <div class="mb-2">
                                <i class="fas fa-upload fa-2x text-primary"></i>
                            </div>
                            <h6>Step 1</h6>
                            <p class="text-muted small">Upload SPM result / fill profile</p>
                        </div>

                        <div class="col-md-3 text-center mb-3">
                            <div class="mb-2">
                                <i class="fas fa-cogs fa-2x text-warning"></i>
                            </div>
                            <h6>Step 2</h6>
                            <p class="text-muted small">System auto-match scholarships</p>
                        </div>

                        <div class="col-md-3 text-center mb-3">
                            <div class="mb-2">
                                <i class="fas fa-star fa-2x text-success"></i>
                            </div>
                            <h6>Step 3</h6>
                            <p class="text-muted small">View recommended scholarships</p>
                        </div>

                        <div class="col-md-3 text-center mb-3">
                            <div class="mb-2">
                                <i class="fas fa-bookmark fa-2x text-danger"></i>
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
                        <h4>Featured Scholarships</h4>
                        <a href="{{ route('scholarship.recommendations') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <div class="row">
                        @php
                            $featuredScholarships = \App\Models\Scholarship::where('deadline', '>', now())
                                ->inRandomOrder()
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        @foreach($featuredScholarships as $scholarship)
                        <div class="col-md-4 mb-4">
                            <div class="scholarship-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary">{{ $scholarship->provider }}</span>
                                    @if($scholarship->deadline->diffInDays(now()) < 7)
                                        <span class="badge bg-danger">Closing Soon</span>
                                    @endif
                                </div>
                                <h6 class="mb-2">{{ Str::limit($scholarship->name, 40) }}</h6>
                                <p class="text-muted small mb-3">{{ Str::limit($scholarship->description, 80) }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        @if($scholarship->amount)
                                            <span class="fw-bold text-success">RM {{ number_format($scholarship->amount) }}</span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Deadline:</small>
                                        <small class="fw-bold">{{ $scholarship->deadline->format('d M Y') }}</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ $scholarship->application_link }}" target="_blank" class="btn btn-sm btn-primary flex-grow-1">Apply</a>
                                    <form action="{{ route('bookmarks.toggle', $scholarship->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
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
        </div>
    </div>
</div>


<!-- Add FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    // Update time and date dynamically
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateStr = now.toLocaleDateString('en-US', options);
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        
        // Update elements if they exist
        const timeElement = document.querySelector('.time-date-widget .fa-clock').parentElement;
        const dateElement = document.querySelector('.time-date-widget .fa-calendar').parentElement;
        
        if(timeElement) {
            timeElement.innerHTML = `<i class="fas fa-clock me-2"></i> ${timeStr}`;
        }
        if(dateElement) {
            dateElement.innerHTML = `<i class="fas fa-calendar me-2"></i> ${now.toLocaleDateString()}`;
        }
    }
    
    // Update every minute
    setInterval(updateDateTime, 60000);
    updateDateTime(); // Initial call
</script>
@endsection