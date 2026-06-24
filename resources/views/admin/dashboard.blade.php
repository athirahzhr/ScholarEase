@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    /* ===== ROOT VARIABLES ===== */
    :root {
        --maroon: #7A0019;
        --maroon-dark: #4e0010;
        --maroon-light: #9e1e32;
        --gold: #F4C542;
        --gold-light: #ffda77;
        --cream: #FFF8EE;
        --cream-dark: #f5ebe0;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --radius: 16px;
        --radius-sm: 8px;
        --radius-lg: 24px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== WELCOME HEADER ===== */
    .dashboard-welcome {
        background: linear-gradient(135deg, #FFF8EE 0%, #f5ebe0 100%);
        border-radius: var(--radius-lg);
        padding: 2rem 2rem 1.75rem;
        margin-bottom: 2rem;
        border-left: 5px solid var(--gold);
        position: relative;
        overflow: hidden;
    }

    .dashboard-welcome::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -5%;
        width: 300px;
        height: 300px;
        background: rgba(244, 197, 66, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }

    .dashboard-welcome h4 {
        color: var(--maroon);
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: -0.5px;
        position: relative;
        z-index: 1;
    }

    .dashboard-welcome h4 i {
        color: var(--gold);
    }

    .dashboard-welcome p {
        color: var(--gray-600);
        margin-bottom: 0;
        font-size: 0.95rem;
        position: relative;
        z-index: 1;
    }

    .date-badge {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
        padding: 0.5rem 1.25rem;
        border-radius: 40px;
        font-size: 0.85rem;
        color: var(--maroon);
        font-weight: 600;
        box-shadow: var(--shadow);
        border: 1px solid rgba(255, 255, 255, 0.6);
        position: relative;
        z-index: 1;
        white-space: nowrap;
    }

    .date-badge i {
        color: var(--gold);
    }

    /* ===== STAT CARDS ===== */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--radius);
        padding: 1.5rem 1.5rem 1rem;
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: 1px solid rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--maroon), var(--gold));
        opacity: 0;
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(244, 197, 66, 0.2);
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--gray-500);
        font-weight: 600;
        display: block;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--gray-900);
        line-height: 1.2;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .stat-value.maroon {
        color: var(--maroon);
    }

    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(122, 0, 25, 0.06);
        transition: var(--transition);
    }

    .stat-card:hover .stat-icon-box {
        background: rgba(122, 0, 25, 0.12);
        transform: scale(1.05);
    }

    .stat-icon-box i {
        font-size: 22px;
        color: var(--maroon);
    }

    .stat-footer {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--gray-100);
    }

    .stat-trend {
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-trend i {
        font-size: 0.7rem;
    }

    .stat-trend .trend-up {
        color: #059669;
    }

    .stat-trend .trend-down {
        color: #dc2626;
    }

    /* ===== CARD STYLES ===== */
    .dashboard-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: var(--transition);
        height: 100%;
    }

    .dashboard-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .dashboard-card .card-header {
        background: transparent;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dashboard-card .card-header h6 {
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dashboard-card .card-header h6 i {
        color: var(--maroon);
    }

    .card-header-badge {
        background: var(--gray-100);
        color: var(--gray-600);
        font-size: 0.65rem;
        padding: 0.2rem 0.6rem;
        border-radius: 40px;
        font-weight: 600;
    }

    .dashboard-card .card-body {
        padding: 0;
    }

    /* ===== LIST ITEMS ===== */
    .list-item {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        transition: var(--transition);
        cursor: default;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item:hover {
        background: var(--gray-50);
    }

    .list-item .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-right: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        color: white;
        background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
        border: 2px solid var(--gold);
    }

    .list-item .avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .list-item .item-content {
        flex: 1;
        min-width: 0;
    }

    .list-item .item-title {
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .list-item .item-subtitle {
        font-size: 0.8rem;
        color: var(--gray-500);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .list-item .item-subtitle i {
        font-size: 0.7rem;
        color: var(--gray-400);
    }

    .list-item .item-meta {
        text-align: right;
        flex-shrink: 0;
        margin-left: 1rem;
    }

    .badge-role {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.6rem;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-role.admin {
        background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
        color: white;
    }

    .badge-role.user {
        background: var(--gray-100);
        color: var(--gray-600);
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.6rem;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-status.active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-status.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-status::before {
        content: '';
        display: inline-block;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        margin-right: 0.4rem;
    }

    .badge-status.active::before {
        background: #059669;
    }

    .badge-status.inactive::before {
        background: #dc2626;
    }

    .item-time {
        font-size: 0.7rem;
        color: var(--gray-400);
        display: block;
        margin-top: 0.15rem;
    }

    /* ===== CARD FOOTER ===== */
    .dashboard-card .card-footer {
        background: var(--gray-50);
        padding: 0.75rem 1.5rem;
        border-top: 1px solid var(--gray-100);
    }

    .btn-outline-maroon {
        color: var(--maroon);
        border: 1px solid var(--maroon);
        background: transparent;
        padding: 0.4rem 1rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-outline-maroon:hover {
        background: var(--maroon);
        color: white;
        border-color: var(--maroon);
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    /* ===== QUICK ACTIONS ===== */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    .quick-action-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem 1rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: var(--transition);
        border: 1px solid transparent;
    }

    .quick-action-item:hover {
        background: white;
        border-color: var(--gold);
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        text-decoration: none;
    }

    .quick-action-item .icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(122, 0, 25, 0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        transition: var(--transition);
    }

    .quick-action-item:hover .icon-wrapper {
        background: rgba(122, 0, 25, 0.12);
        transform: scale(1.05);
    }

    .quick-action-item .icon-wrapper i {
        font-size: 24px;
        color: var(--maroon);
    }

    .quick-action-item .action-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--gray-700);
        margin: 0;
    }

    .quick-action-item .action-desc {
        font-size: 0.7rem;
        color: var(--gray-400);
        margin: 0.2rem 0 0;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-state i {
        font-size: 48px;
        color: var(--gray-300);
        display: block;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: var(--gray-500);
        margin: 0;
        font-size: 0.9rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .quick-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .dashboard-welcome {
            padding: 1.25rem;
        }

        .dashboard-welcome h4 {
            font-size: 1.2rem;
        }

        .dashboard-welcome .d-flex {
            flex-direction: column;
            align-items: stretch !important;
            gap: 0.75rem;
        }

        .date-badge {
            align-self: flex-start;
        }

        .stat-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .stat-card {
            padding: 1.25rem 1rem 0.75rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-icon-box {
            width: 40px;
            height: 40px;
        }

        .stat-icon-box i {
            font-size: 18px;
        }

        .quick-actions-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .quick-action-item {
            padding: 1rem;
        }

        .quick-action-item .icon-wrapper {
            width: 44px;
            height: 44px;
        }

        .quick-action-item .icon-wrapper i {
            font-size: 20px;
        }

        .list-item {
            padding: 0.75rem 1rem;
            flex-wrap: wrap;
        }

        .list-item .item-meta {
            margin-left: 0;
            width: 100%;
            margin-top: 0.5rem;
            text-align: left;
        }

        .dashboard-card .card-header {
            padding: 1rem 1rem;
        }

        .dashboard-card .card-footer {
            padding: 0.75rem 1rem;
        }
    }

    @media (max-width: 480px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions-grid {
            grid-template-columns: 1fr;
        }

        .list-item .item-subtitle {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }
    }

    /* ===== UTILITY ===== */
    .gap-2 {
        gap: 0.5rem;
    }

    .gap-3 {
        gap: 0.75rem;
    }

    .text-muted-light {
        color: var(--gray-400);
    }

    .fw-600 {
        font-weight: 600;
    }
</style>

<div class="container-fluid px-0">
    <div class="row">
        <div class="col-12">

            <!-- ===== WELCOME HEADER ===== -->
            <div class="dashboard-welcome">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4>
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Admin Dashboard
                        </h4>
                        <p>
                            <i class="fas fa-chart-line me-1" style="color: var(--gold);"></i>
                            Welcome back, <strong>{{ auth()->user()->name }}</strong>! 
                            Here's your platform overview for today.
                        </p>
                    </div>
                    <div class="date-badge">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span id="dateDisplay">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- ===== STATS CARDS ===== -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-card-content">
                        <div class="stat-info">
                            <span class="stat-label">Total Users</span>
                            <h3 class="stat-value maroon">{{ $totalUsers ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon-box">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-trend">
                            <i class="fas fa-user-plus trend-up"></i>
                            <span class="trend-up">+{{ $newUsersThisMonth ?? 0 }}</span> this month
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-content">
                        <div class="stat-info">
                            <span class="stat-label">Total Scholarships</span>
                            <h3 class="stat-value maroon">{{ $totalScholarships ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon-box">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-trend">
                            <i class="fas fa-check-circle trend-up"></i>
                            <span class="trend-up">{{ $activeScholarships ?? 0 }}</span> active
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-content">
                        <div class="stat-info">
                            <span class="stat-label">Total Bookmarks</span>
                            <h3 class="stat-value maroon">{{ $totalBookmarks ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon-box">
                            <i class="fas fa-bookmark"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-trend">
                            <i class="fas fa-heart trend-up"></i>
                            Student interest
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-content">
                        <div class="stat-info">
                            <span class="stat-label">Expiring Soon</span>
                            <h3 class="stat-value maroon">{{ $expiringSoon ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon-box">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-trend">
                            <i class="fas fa-hourglass-half trend-down"></i>
                            Within 7 days
                        </span>
                    </div>
                </div>
            </div>

            <!-- ===== RECENT ACTIVITY ROW ===== -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h6>
                                <i class="fas fa-users"></i>
                                Recent Users
                            </h6>
                            <span class="card-header-badge">
                                {{ isset($recentUsers) ? $recentUsers->count() : 0 }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if(isset($recentUsers) && $recentUsers->count() > 0)
                                @foreach($recentUsers as $user)
                                    <div class="list-item">
                                        <div class="avatar">
                                            @if($user->profile && $user->profile->avatar)
                                                <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}">
                                            @else
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="item-content">
                                            <p class="item-title">{{ $user->name }}</p>
                                            <p class="item-subtitle">
                                                <span>{{ $user->email }}</span>
                                                <span>
                                                    <i class="fas fa-clock"></i>
                                                    {{ $user->created_at->diffForHumans() }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="item-meta">
                                            <span class="badge-role {{ $user->role === 'admin' ? 'admin' : 'user' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No users registered yet</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('admin.users.index') }}" class="btn-outline-maroon">
                                <i class="fas fa-arrow-right"></i>
                                View All Users
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h6>
                                <i class="fas fa-graduation-cap"></i>
                                Recent Scholarships
                            </h6>
                            <span class="card-header-badge">
                                {{ isset($recentScholarships) ? $recentScholarships->count() : 0 }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if(isset($recentScholarships) && $recentScholarships->count() > 0)
                                @foreach($recentScholarships as $scholarship)
                                    <div class="list-item">
                                        <div class="item-content">
                                            <p class="item-title">
                                                <i class="fas fa-award" style="color: var(--gold); margin-right: 0.4rem;"></i>
                                                {{ Str::limit($scholarship->title, 50) }}
                                            </p>
                                            <p class="item-subtitle">
                                                <span>
                                                    <i class="fas fa-building"></i>
                                                    {{ $scholarship->provider }}
                                                </span>
                                                @if($scholarship->deadline)
                                                    <span>
                                                        <i class="fas fa-calendar-alt"></i>
                                                        {{ \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="item-meta">
                                            <span class="badge-status {{ $scholarship->is_active ? 'active' : 'inactive' }}">
                                                {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span class="item-time">
                                                <i class="fas fa-clock"></i>
                                                {{ $scholarship->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No scholarships added yet</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('admin.scholarships.index') }}" class="btn-outline-maroon">
                                <i class="fas fa-arrow-right"></i>
                                Manage Scholarships
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== QUICK ACTIONS ===== -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h6>
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h6>
                    <span class="card-header-badge">⚡</span>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.scholarships.create') }}" class="quick-action-item">
                            <div class="icon-wrapper">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <p class="action-label">Add Scholarship</p>
                            <p class="action-desc">Create new opportunity</p>
                        </a>

                        <a href="{{ route('admin.scraper.index') }}" class="quick-action-item">
                            <div class="icon-wrapper">
                                <i class="fas fa-robot"></i>
                            </div>
                            <p class="action-label">Run Scraper</p>
                            <p class="action-desc">Fetch latest scholarships</p>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="quick-action-item">
                            <div class="icon-wrapper">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <p class="action-label">Manage Users</p>
                            <p class="action-desc">View & manage accounts</p>
                        </a>

                        <a href="{{ route('admin.scraping.logs') }}" class="quick-action-item">
                            <div class="icon-wrapper">
                                <i class="fas fa-history"></i>
                            </div>
                            <p class="action-label">View Logs</p>
                            <p class="action-desc">Monitor system activity</p>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format and display current date
        const dateElement = document.getElementById('dateDisplay');
        if (dateElement) {
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            dateElement.textContent = new Date().toLocaleDateString('en-US', options);
        }

        // Optional: Add smooth entrance animation for stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 80));
        });
    });
</script>
@endpush

@endsection