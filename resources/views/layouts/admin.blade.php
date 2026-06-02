<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - ScholarEase Admin</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
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
            --white: #ffffff;
            --sidebar-width: 280px;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: var(--cream);
            color: var(--gray-800);
        }
        
        /* Sidebar Styles - ScholarEase Branding */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--maroon) 0%, var(--maroon-dark) 100%);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            padding-top: 20px;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }
        
        .sidebar-header h4 i {
            color: var(--gold);
            margin-right: 8px;
        }
        
        .sidebar-header .badge {
            font-size: 0.7em;
            padding: 0.25em 0.6em;
            background: var(--gold) !important;
            color: var(--maroon-dark) !important;
            font-weight: 700;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 12px 25px;
            margin: 5px 15px;
            border-radius: 12px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.15);
            text-decoration: none;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: rgba(244, 197, 66, 0.2);
            color: var(--gold);
            font-weight: 600;
            border-left: 3px solid var(--gold);
        }
        
        .nav-link i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
            font-size: 1.1em;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 15px 25px;
            margin: -20px -20px 25px -20px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 2px solid var(--gold);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--maroon);
        }
        
        .top-navbar h5 {
            color: var(--maroon);
            font-weight: 700;
        }
        
        .user-dropdown img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid var(--gold);
        }
        
        /* Stats Cards - ScholarEase Style */
        .stat-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
            position: relative;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--maroon));
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(244,197,66,0.1));
            color: var(--maroon);
        }
        
        .stat-card h3 {
            color: var(--maroon);
            font-weight: 800;
        }
        
        /* Table Styles */
        .table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--maroon);
            background: linear-gradient(135deg, var(--cream), var(--cream-dark));
            padding: 15px;
            border-bottom: 2px solid var(--gold);
        }
        
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(244, 197, 66, 0.08);
            cursor: pointer;
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 60px;
            font-weight: 600;
            transition: all 0.3s ease;
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
            border-radius: 60px;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: var(--maroon);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: linear-gradient(115deg, #dc2626, #b91c1c);
            border: none;
            border-radius: 60px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        }
        
        /* Badge Styles */
        .badge-admin {
            background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .badge-user {
            background: linear-gradient(135deg, #4cc9f0, #4361ee);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--cream), var(--cream-dark));
            border-bottom: 2px solid var(--gold);
            padding: 15px 20px;
            font-weight: 700;
            color: var(--maroon);
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 16px;
            border: none;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border-left-color: #10b981;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border-left-color: #dc2626;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
            outline: none;
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            border-radius: 16px;
            border: 1px solid rgba(122, 0, 25, 0.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-item:hover {
            background: rgba(122, 0, 25, 0.05);
            color: var(--maroon);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar .nav-text {
                display: none;
            }
            
            .sidebar .nav-link {
                justify-content: center;
                padding: 12px 0;
                margin: 5px 10px;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.2em;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .sidebar-header h4 {
                font-size: 1.2em;
            }
            
            .sidebar-header h4 span {
                display: none;
            }
            
            .top-navbar h5 {
                font-size: 1rem;
            }
        }
        
        /* Pagination Styles */
        .pagination .page-link {
            color: var(--maroon);
            border-radius: 8px;
            margin: 0 3px;
            border: 1px solid #e5e7eb;
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
            border-color: var(--maroon);
            color: white;
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--cream), var(--cream-dark));
            border-bottom: 2px solid var(--gold);
            border-radius: 20px 20px 0 0;
        }
        
        .modal-title {
            color: var(--maroon);
            font-weight: 700;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-crown me-2"></i> <span>ScholarEase</span></h4>
            <span class="badge mt-2">Admin Panel</span>
        </div>
        
        <div class="sidebar-menu">
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
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.feedbacks*') ? 'active' : '' }}" 
                    href="{{ route('admin.feedbacks') }}">
                        <i class="fas fa-star"></i>
                        <span class="nav-text">Student Feedbacks</span>
                        @php
                            $pendingFeedbacks = \App\Models\Feedback::where('approved', 0)->count();
                        @endphp
                        @if($pendingFeedbacks > 0)
                            <span class="badge ms-auto" style="background: #fef3c7; color: #92400e; border-radius: 20px; padding: 2px 8px; font-size: 0.7rem;">
                                {{ $pendingFeedbacks }}
                            </span>
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
                <li class="nav-item mt-4">
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar">
            <div class="container-fluid">
                <button class="navbar-toggler d-lg-none" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-maroon"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    <h5 class="mb-0">@yield('title')</h5>
                </div>
                
                <div class="navbar-nav ms-auto">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                           id="userDropdown" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=7A0019&color=F4C542&bold=true" 
                                 alt="Profile">
                            <div class="d-none d-md-block">
                                <div class="fw-medium">{{ auth()->user()->name }}</div>
                                <small class="text-muted">Administrator</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid px-0">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({ duration: 800, once: true });
        
        // CSRF Token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.main-content');
            
            if (sidebar.style.width === '70px' || sidebar.style.width === '') {
                sidebar.style.width = '280px';
                content.style.marginLeft = '280px';
            } else {
                sidebar.style.width = '70px';
                content.style.marginLeft = '70px';
            }
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>