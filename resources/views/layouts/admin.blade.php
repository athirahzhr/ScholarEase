<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Scholarship Admin</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            padding-top: 20px;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-header .badge {
            font-size: 0.7em;
            padding: 0.25em 0.6em;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 25px;
            margin: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            text-decoration: none;
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            font-weight: 500;
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
            padding: 15px 0;
            margin: -20px -20px 20px -20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-dropdown img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        /* Stats Cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
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
        }
        
        /* Table Styles */
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #f72585, #b5179e);
            color: white;
        }
        
        .badge-user {
            background: linear-gradient(135deg, #4cc9f0, #4361ee);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-crown me-2"></i>Admin Panel</h4>
            <span class="badge bg-light text-primary mt-2">v1.0</span>
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
                    <a class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Manage Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('scholarships.*') ? 'active' : '' }}" 
                       href="{{ route('admin.scholarships.index') }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span class="nav-text">Scholarships</span>
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
                        <span class="nav-text">Notifications Center</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('scholarships.create') ? 'active' : '' }}" 
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
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 text-muted">@yield('title')</h5>
                </div>
                
                <div class="navbar-nav ms-auto">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                           id="userDropdown" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=4361ee&color=fff" 
                                 alt="Profile">
                            <div class="d-none d-md-block">
                                <div class="fw-medium">{{ auth()->user()->name }}</div>
                                <small class="text-muted">Administrator</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
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
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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