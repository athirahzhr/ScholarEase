<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - ScholarEase Admin</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* ========================================
                   ROOT VARIABLES
                   ======================================== */
        :root {
            --maroon: #7A0019;
            --maroon-dark: #4e0010;
            --maroon-light: #9e1e32;
            --maroon-rgb: 122, 0, 25;
            --gold: #F4C542;
            --gold-light: #ffda77;
            --gold-rgb: 244, 197, 66;
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
            --white: #ffffff;
            --sidebar-width: 280px;
            --sidebar-collapsed: 72px;
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ========================================
                   BASE STYLES
                   ======================================== */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: var(--gray-50);
            color: var(--gray-800);
            overflow-x: hidden;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--maroon);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--maroon-dark);
        }

        /* ========================================
                   SIDEBAR STYLES
                   ======================================== */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--maroon) 0%, var(--maroon-dark) 100%);
            color: white;
            transition: var(--transition);
            z-index: 1050;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.15);
        }

        .sidebar::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Sidebar Header */
        .sidebar-header {
            padding: 24px 20px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .sidebar-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 20%;
            right: 20%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 42px;
            height: 42px;
            background: rgba(244, 197, 66, 0.15);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--gold);
            flex-shrink: 0;
            transition: var(--transition);
        }

        .sidebar-brand-text {
            font-size: 1.35rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .sidebar-brand-text span {
            color: var(--gold);
        }

        .sidebar-badge {
            display: inline-block;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            background: var(--gold);
            color: var(--maroon-dark);
            border-radius: 40px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 6px;
        }

        /* Sidebar Menu */
        .sidebar-menu {
            padding: 16px 12px 20px;
        }

        .sidebar-menu-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.35);
            padding: 0 12px 8px;
            font-weight: 600;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 10px 14px;
            margin: 2px 0;
            border-radius: var(--radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: rgba(244, 197, 66, 0.15);
            color: var(--gold);
            font-weight: 600;
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: var(--gold);
            border-radius: 0 4px 4px 0;
        }

        .sidebar .nav-link i {
            width: 28px;
            text-align: center;
            font-size: 1.05rem;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.1);
        }

        .sidebar .nav-link .nav-text {
            margin-left: 4px;
            white-space: nowrap;
        }

        .sidebar .nav-link .badge-notification {
            background: #fef3c7;
            color: #92400e;
            border-radius: 40px;
            padding: 2px 8px;
            font-size: 0.65rem;
            font-weight: 700;
            margin-left: auto;
        }

        .sidebar .nav-link.text-danger {
            color: rgba(239, 68, 68, 0.8) !important;
        }

        .sidebar .nav-link.text-danger:hover {
            color: #ef4444 !important;
            background: rgba(239, 68, 68, 0.12);
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.06);
            margin: 12px 14px;
        }

        /* ========================================
                   MAIN CONTENT
                   ======================================== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 0;
            transition: var(--transition);
            background: var(--gray-50);
        }

        /* ========================================
                   TOP NAVBAR
                   ======================================== */
        .top-navbar {
            background: white;
            padding: 12px 28px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 1040;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: var(--shadow-xs);
        }

        .top-navbar .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
        }

        .top-navbar .page-title i {
            color: var(--maroon);
            margin-right: 8px;
        }

        .top-navbar .breadcrumb {
            margin: 0;
            padding: 0;
            background: transparent;
            font-size: 0.8rem;
        }

        .top-navbar .breadcrumb-item a {
            color: var(--gray-500);
            text-decoration: none;
            transition: var(--transition);
        }

        .top-navbar .breadcrumb-item a:hover {
            color: var(--maroon);
        }

        .top-navbar .breadcrumb-item.active {
            color: var(--gray-700);
            font-weight: 600;
        }

        /* User Dropdown */
        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            padding: 6px 12px 6px 6px;
            border-radius: 40px;
            transition: var(--transition);
            background: transparent;
            border: none;
        }

        .user-dropdown-toggle:hover {
            background: var(--gray-100);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid var(--gold);
            object-fit: cover;
            flex-shrink: 0;
        }

        .user-dropdown-toggle .user-info {
            text-align: left;
        }

        .user-dropdown-toggle .user-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--gray-800);
            line-height: 1.2;
        }

        .user-dropdown-toggle .user-role {
            font-size: 0.7rem;
            color: var(--gray-500);
        }

        .user-dropdown-toggle .dropdown-arrow {
            color: var(--gray-400);
            font-size: 0.7rem;
            transition: var(--transition);
        }

        .user-dropdown-toggle[aria-expanded="true"] .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* ========================================
                   CONTENT WRAPPER
                   ======================================== */
        .content-wrapper {
            padding: 24px 28px 40px;
        }

        /* ========================================
                   ALERT STYLES
                   ======================================== */
        .alert-custom {
            border: none;
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-sm);
        }

        .alert-custom .alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .alert-custom .alert-content {
            flex: 1;
        }

        .alert-custom .alert-content strong {
            display: block;
            font-size: 0.9rem;
        }

        .alert-custom .alert-content p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .alert-success-custom {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-left: 4px solid #10b981;
        }

        .alert-success-custom .alert-icon {
            background: #10b981;
            color: white;
        }

        .alert-success-custom .alert-content strong {
            color: #065f46;
        }

        .alert-success-custom .alert-content p {
            color: #047857;
        }

        .alert-danger-custom {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border-left: 4px solid #ef4444;
        }

        .alert-danger-custom .alert-icon {
            background: #ef4444;
            color: white;
        }

        .alert-danger-custom .alert-content strong {
            color: #991b1b;
        }

        .alert-danger-custom .alert-content p {
            color: #b91c1c;
        }

        /* ========================================
                   RESPONSIVE
                   ======================================== */
        @media (max-width: 992px) {
            .sidebar {
                width: var(--sidebar-collapsed);
            }

            .sidebar .nav-text,
            .sidebar .sidebar-badge,
            .sidebar .sidebar-menu-label,
            .sidebar .badge-notification {
                display: none !important;
            }

            .sidebar .nav-link {
                justify-content: center;
                padding: 10px;
                margin: 2px 4px;
            }

            .sidebar .nav-link i {
                margin: 0;
                font-size: 1.15rem;
            }

            .sidebar .nav-link.active::before {
                display: none;
            }

            .sidebar .sidebar-brand-text {
                display: none;
            }

            .sidebar .sidebar-brand-icon {
                width: 44px;
                height: 44px;
                font-size: 22px;
            }

            .main-content {
                margin-left: var(--sidebar-collapsed);
            }

            .top-navbar {
                padding: 10px 16px;
            }

            .content-wrapper {
                padding: 16px;
            }

            .user-dropdown-toggle .user-info {
                display: none;
            }

            .user-dropdown-toggle {
                padding: 4px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                width: var(--sidebar-width);
                transform: translateX(0);
            }

            .sidebar.mobile-open .nav-text,
            .sidebar.mobile-open .sidebar-badge,
            .sidebar.mobile-open .sidebar-menu-label,
            .sidebar.mobile-open .badge-notification {
                display: inline-block !important;
            }

            .sidebar.mobile-open .nav-link {
                justify-content: flex-start;
                padding: 10px 14px;
            }

            .sidebar.mobile-open .nav-link i {
                margin-right: 10px;
            }

            .sidebar.mobile-open .sidebar-brand-text {
                display: inline;
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 12px;
            }

            .top-navbar .page-title {
                font-size: 0.95rem;
            }

            .mobile-toggle {
                display: flex !important;
            }
        }

        .mobile-toggle {
            display: none;
            background: transparent;
            border: none;
            color: var(--gray-700);
            font-size: 1.3rem;
            padding: 4px 8px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .mobile-toggle:hover {
            background: var(--gray-100);
        }

        /* ========================================
                   SCROLLBAR STYLING FOR SIDEBAR
                   ======================================== */
        .sidebar::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        /* ========================================
                   DARK OVERLAY FOR MOBILE
                   ======================================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1045;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.active {
            display: block;
        }

        @media (max-width: 576px) {
            .sidebar-overlay.active {
                display: block;
            }
        }

        /* ========================================
                   UTILITY CLASSES
                   ======================================== */
        .text-maroon {
            color: var(--maroon) !important;
        }

        .text-gold {
            color: var(--gold) !important;
        }

        .bg-maroon-light {
            background: rgba(var(--maroon-rgb), 0.06);
        }

        .bg-gold-light {
            background: rgba(var(--gold-rgb), 0.06);
        }

        .fw-600 {
            font-weight: 600;
        }

        .fw-700 {
            font-weight: 700;
        }

        .fw-800 {
            font-weight: 800;
        }

        .gap-1 {
            gap: 0.25rem;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        /* ========================================
                   RESPONSIVE TABLE WRAPPER
                   ======================================== */
        .table-responsive-custom {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* ========================================
                   LOADING SPINNER
                   ======================================== */
        .spinner-maroon {
            width: 2.5rem;
            height: 2.5rem;
            border: 3px solid rgba(var(--maroon-rgb), 0.1);
            border-top-color: var(--maroon);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ========================================
                   PRINT STYLES
                   ======================================== */
        @media print {
            .sidebar,
            .top-navbar {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            
            .content-wrapper {
                padding: 20px !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- ============================================
    SIDEBAR
    ============================================ -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <span class="sidebar-brand-text">Scholar<span>Ease</span></span>
            </a>
            <span class="sidebar-badge">Admin Panel</span>
        </div>
        
        <div class="sidebar-menu">
            <div class="sidebar-menu-label">Main</div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Manage Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.scholarships.*') ? 'active' : '' }}" 
                       href="{{ route('admin.scholarships.index') }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span class="nav-text">Scholarships</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-divider"></div>
            <div class="sidebar-menu-label">Management</div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.feedbacks*') ? 'active' : '' }}" 
                       href="{{ route('admin.feedbacks') }}">
                        <i class="fas fa-star"></i>
                        <span class="nav-text">Student Feedbacks</span>
                        @php
                            $pendingFeedbacks = \App\Models\Feedback::where('approved', 0)->count();
                        @endphp
                        @if($pendingFeedbacks > 0)
                            <span class="badge-notification">{{ $pendingFeedbacks }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.scraping-logs') ? 'active' : '' }}" 
                       href="{{ route('admin.scraping.logs') }}">
                        <i class="fas fa-robot"></i>
                        <span class="nav-text">Scraping Logs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.scraper') ? 'active' : '' }}" 
                       href="{{ route('admin.scraper.index') }}">
                        <i class="fas fa-play-circle"></i>
                        <span class="nav-text">Run Scraper</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.notifications') ? 'active' : '' }}" 
                       href="{{ route('admin.notifications') }}">
                        <i class="fas fa-bell"></i>
                        <span class="nav-text">Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.scholarships.create') ? 'active' : '' }}" 
                       href="{{ route('admin.scholarships.create') }}">
                        <i class="fas fa-plus-circle"></i>
                        <span class="nav-text">Add Scholarship</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-divider"></div>
            <div class="sidebar-menu-label">Account</div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Back to Site</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-text">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ============================================
    MAIN CONTENT
    ============================================ -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button class="mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div>
                        <h5 class="page-title">
                            <i class="fas fa-{{ request()->routeIs('admin.dashboard') ? 'tachometer-alt' : (request()->routeIs('admin.users.*') ? 'users' : (request()->routeIs('admin.scholarships.*') ? 'graduation-cap' : 'star')) }}"></i>
                            @yield('title')
                        </h5>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Quick Actions -->
                    <div class="d-none d-md-flex align-items-center gap-2">
                        <a href="{{ route('admin.scholarships.create') }}" class="btn btn-sm btn-primary" style="border-radius: 40px; padding: 0.35rem 1rem; font-size: 0.8rem;">
                            <i class="fas fa-plus-circle me-1"></i> New Scholarship
                        </a>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="user-dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="user-avatar" 
                                 src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=7A0019&color=F4C542&bold=true&size=38" 
                                 alt="{{ auth()->user()->name }}">
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name }}</div>
                                <div class="user-role">Administrator</div>
                            </div>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="border-radius: var(--radius-lg); padding: 8px; min-width: 200px; box-shadow: var(--shadow-lg); border: 1px solid rgba(0,0,0,0.04);">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.index') }}" style="border-radius: var(--radius-sm); padding: 8px 14px;">
                                    <i class="fas fa-user me-2" style="width: 18px; color: var(--maroon);"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" style="border-radius: var(--radius-sm); padding: 8px 14px;">
                                    <i class="fas fa-cog me-2" style="width: 18px; color: var(--maroon);"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   style="border-radius: var(--radius-sm); padding: 8px 14px; font-weight: 600;">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert-custom alert-success-custom mb-4" role="alert" data-aos="fade-down" data-aos-duration="500">
                    <div class="alert-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="alert-content">
                        <strong>Success!</strong>
                        <p>{{ session('success') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.7rem;"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert-custom alert-danger-custom mb-4" role="alert" data-aos="fade-down" data-aos-duration="500">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation"></i>
                    </div>
                    <div class="alert-content">
                        <strong>Error!</strong>
                        <p>{{ session('error') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.7rem;"></button>
                </div>
            @endif
            
            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- ============================================
    SCRIPTS
    ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // ============================================
        // INITIALIZE AOS
        // ============================================
        AOS.init({
            duration: 600,
            once: true,
            offset: 20,
            easing: 'ease-out-cubic'
        });

        // ============================================
        // CSRF TOKEN FOR AJAX
        // ============================================
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ============================================
        // SIDEBAR TOGGLE
        // ============================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
            
            // Prevent body scroll when sidebar is open
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // ============================================
        // CLOSE SIDEBAR ON ESCAPE KEY
        // ============================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // ============================================
        // AUTO-DISMISS ALERTS
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-custom');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // ============================================
        // HANDLE SIDEBAR COLLAPSE ON RESIZE
        // ============================================
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 576) {
                    closeSidebar();
                }
            }, 250);
        });

        // ============================================
        // TOOLTIP INITIALIZATION (if needed)
        // ============================================
        // const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        // tooltipTriggerList.map(function (tooltipTriggerEl) {
        //     return new bootstrap.Tooltip(tooltipTriggerEl);
        // });
    </script>
    
    @stack('scripts')
</body>
</html>