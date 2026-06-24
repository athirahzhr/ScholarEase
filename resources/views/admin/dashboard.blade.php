@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-md-12">
            <!-- Welcome Header -->
            <div class="welcome-header mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1" style="color: #7A0019; font-weight: 700;">
                            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                        </h4>
                        <p class="text-muted mb-0">
                            <i class="fas fa-chart-line me-1"></i> Welcome back, {{ auth()->user()->name }}! Here's what's happening with your platform today.
                        </p>
                    </div>
                    <div class="date-badge" id="currentDate">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span id="dateDisplay">Loading...</span>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <span class="stat-label">Total Users</span>
                                <h3 class="stat-value">{{ $totalUsers ?? 0 }}</h3>
                            </div>
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-users stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span class="stat-trend">
                                <i class="fas fa-user-plus me-1"></i> 
                                +{{ $newUsersThisMonth ?? 0 }} this month
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-card-success">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <span class="stat-label">Total Scholarships</span>
                                <h3 class="stat-value">{{ $totalScholarships ?? 0 }}</h3>
                            </div>
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-graduation-cap stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span class="stat-trend">
                                <i class="fas fa-plus-circle me-1"></i> 
                                {{ $activeScholarships ?? 0 }} active
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-card-warning">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <span class="stat-label">Total Bookmarks</span>
                                <h3 class="stat-value">{{ $totalBookmarks ?? 0 }}</h3>
                            </div>
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-bookmark stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span class="stat-trend">
                                <i class="fas fa-chart-line me-1"></i> 
                                Student interest
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-card-info">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <span class="stat-label">Expiring Soon</span>
                                <h3 class="stat-value">{{ $expiringSoon ?? 0 }}</h3>
                            </div>
                            <div class="stat-icon-wrapper">
                                <i class="fas fa-clock stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span class="stat-trend">
                                <i class="fas fa-hourglass-half me-1"></i> 
                                Within 7 days
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Section -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-users me-2"></i> Recent Users
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            @if(isset($recentUsers) && $recentUsers->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentUsers as $user)
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    @if($user->profile && $user->profile->avatar)
                                                        <img src="{{ asset('storage/' . $user->profile->avatar) }}" 
                                                             alt="{{ $user->name }}" 
                                                             width="40" 
                                                             height="40" 
                                                             class="rounded-circle"
                                                             style="object-fit: cover; border: 2px solid #F4C542;">
                                                    @else
                                                        <div class="avatar-initial rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px; background: linear-gradient(135deg, #7A0019, #4e0010); color: white; font-weight: 700;">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-0" style="color: #7A0019;">{{ $user->name }}</h6>
                                                            <small class="text-muted">{{ $user->email }}</small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="role-badge {{ $user->role === 'admin' ? 'role-admin' : 'role-user' }}">
                                                                {{ ucfirst($user->role) }}
                                                            </span>
                                                            <div class="small text-muted mt-1">
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ $user->created_at->diffForHumans() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-view-all">
                                        <i class="fas fa-arrow-right me-1"></i> View All Users
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-user-slash" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                    <p class="text-muted">No users registered yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-graduation-cap me-2"></i> Recent Scholarships
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            @if(isset($recentScholarships) && $recentScholarships->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentScholarships as $scholarship)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <i class="fas fa-award" style="color: #F4C542;"></i>
                                                        <h6 class="mb-0" style="color: #7A0019;">
                                                            {{ Str::limit($scholarship->title, 45) }}
                                                        </h6>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-building me-1"></i> {{ $scholarship->provider }}
                                                        </small>
                                                        @if($scholarship->deadline)
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar-alt me-1"></i>
                                                                {{ \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="status-badge {{ $scholarship->is_active ? 'status-active' : 'status-inactive' }}">
                                                        {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                    <div class="small text-muted mt-1">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $scholarship->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="{{ route('admin.scholarships.index') }}" class="btn btn-sm btn-view-all">
                                        <i class="fas fa-arrow-right me-1"></i> Manage Scholarships
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                    <p class="text-muted">No scholarships added yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-bolt me-2"></i> Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.scholarships.create') }}" class="quick-action-btn">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>Add Scholarship</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.scraper.index') }}" class="quick-action-btn">
                                        <i class="fas fa-robot"></i>
                                        <span>Run Scraper</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.users.index') }}" class="quick-action-btn">
                                        <i class="fas fa-users"></i>
                                        <span>Manage Users</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.scraping.logs') }}" class="quick-action-btn">
                                        <i class="fas fa-history"></i>
                                        <span>View Logs</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .welcome-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        padding: 20px;
        border-radius: 20px;
        border-left: 4px solid #F4C542;
    }
    
    .date-badge {
        background: white;
        padding: 8px 16px;
        border-radius: 40px;
        font-size: 0.85rem;
        color: #7A0019;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card-inner {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        font-weight: 600;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 5px 0 0 0;
        color: #7A0019;
    }
    
    .stat-icon-wrapper {
        width: 55px;
        height: 55px;
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(244,197,66,0.1));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stat-icon {
        font-size: 28px;
        color: #7A0019;
    }
    
    .stat-footer {
        padding: 12px 20px;
        background: #f9fafb;
        border-top: 1px solid #f0f0f0;
    }
    
    .stat-trend {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    /* Card Styles */
    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-bottom: 2px solid #F4C542;
        padding: 15px 20px;
    }
    
    .card-header h6 {
        color: #7A0019;
        font-weight: 700;
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
        transition: all 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: rgba(244, 197, 66, 0.08);
    }
    
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .role-admin {
        background: linear-gradient(135deg, #7A0019, #4e0010);
        color: white;
    }
    
    .role-user {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .status-active {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    
    .status-inactive {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }
    
    .btn-view-all {
        background: transparent;
        color: #7A0019;
        border: 1px solid #7A0019;
        border-radius: 40px;
        transition: all 0.3s ease;
    }
    
    .btn-view-all:hover {
        background: #7A0019;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Quick Action Buttons */
    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        background: #f9fafb;
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }
    
    .quick-action-btn i {
        font-size: 32px;
        color: #7A0019;
        margin-bottom: 10px;
    }
    
    .quick-action-btn span {
        color: #374151;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-5px);
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-color: #F4C542;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .quick-action-btn:hover i {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
    
    .card-footer {
        background: white;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    @media (max-width: 768px) {
        .stat-value {
            font-size: 1.8rem;
        }
        
        .stat-icon-wrapper {
            width: 45px;
            height: 45px;
        }
        
        .stat-icon {
            font-size: 22px;
        }
        
        .quick-action-btn {
            padding: 15px;
        }
        
        .quick-action-btn i {
            font-size: 24px;
        }
        
        .welcome-header h4 {
            font-size: 1.3rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Function to format date
    function formatDate(date) {
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        return date.toLocaleDateString('en-US', options);
    }
    
    // Get current date
    const today = new Date();
    const dateElement = document.getElementById('dateDisplay');
    
    if (dateElement) {
        dateElement.textContent = formatDate(today);
    }
    
    // Alternative: You can also get server date via AJAX if needed
    // But JavaScript client date is fine for display purposes
</script>
@endpush

@endsection