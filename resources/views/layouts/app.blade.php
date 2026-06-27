<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ScholarEase - @yield('title', 'SPM Scholarship Finder')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
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
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: var(--cream);
            color: var(--gray-800);
        }
        
        .navbar-scholarease {
            background: rgba(255, 248, 238, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(122, 0, 25, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--maroon) 0%, var(--gold) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }
        
        .navbar-brand i {
            background: linear-gradient(135deg, var(--maroon), var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 1.8rem;
        }
        
        .nav-link {
            color: var(--gray-600) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--maroon) !important;
            background: rgba(122, 0, 25, 0.05);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gold);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 80%;
        }
        
        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 1.5rem;
            border-radius: 60px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        
        .btn-nav-login {
            background: rgba(122, 0, 25, 0.08);
            color: var(--maroon) !important;
            border: 1px solid rgba(122, 0, 25, 0.2);
        }
        
        .btn-nav-login:hover {
            background: var(--maroon);
            color: white !important;
            transform: translateY(-2px);
            border-color: transparent;
        }
        
        .btn-nav-register {
            background: linear-gradient(115deg, #ffb347, #ff8c00);
            color: #2c1a00 !important;
            box-shadow: 0 4px 12px rgba(255, 140, 0, 0.3);
        }
        
        .btn-nav-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 140, 0, 0.4);
            background: linear-gradient(115deg, #ffa726, #ff9800);
            color: #1f1400 !important;
        }
        
        .btn-primary {
            background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 60px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--maroon);
            color: white;
            transform: translateY(-2px);
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            border-radius: 16px;
            border: none;
        }
        
        .alert-info {
            background: linear-gradient(135deg, var(--cream), var(--cream-dark));
            color: var(--maroon);
            border-left: 4px solid var(--gold);
        }
        
        footer {
            background: var(--maroon-dark);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 3rem;
        }
        
        footer h5, footer h6 {
            color: var(--gold);
            font-weight: 700;
        }
        
        footer a {
            transition: all 0.2s ease;
        }
        
        footer a:hover {
            color: var(--gold) !important;
            padding-left: 5px;
        }
        
        .dropdown-menu {
            border-radius: 16px;
            border: 1px solid rgba(122, 0, 25, 0.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-item:hover {
            background: rgba(122, 0, 25, 0.05);
            color: var(--maroon);
        }
        
        .dropdown-item.text-danger:hover {
            background: rgba(220, 38, 38, 0.1);
        }
        
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.4rem;
            }
            
            .nav-link::after {
                display: none;
            }
            
            .btn-nav {
                width: 100%;
                justify-content: center;
                margin: 0.25rem 0;
            }
            
            .navbar-nav {
                gap: 0;
            }
        }
        
        .navbar-toggler {
            border: none;
            background: transparent;
        }
        
        .navbar-toggler-icon {
            background-image: none;
            position: relative;
        }
        
        .navbar-toggler-icon i {
            font-size: 1.5rem;
            color: var(--maroon);
        }
        
        .navbar-nav {
            align-items: center;
            gap: 0.5rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-scholarease">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i> ScholarEase
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon">
                    <i class="fas fa-bars"></i>
                </span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('scholarship.finder') }}">
                                <i class="fas fa-search me-1"></i> Find
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('bookmarks.index') }}">
                                <i class="fas fa-bookmark me-1"></i> Bookmarks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('feedback.create') }}">
                                <i class="fas fa-star me-1"></i> Feedback
                            </a>
                        </li>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-crown me-1"></i> Admin
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="fas fa-user me-2"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('feedback.create') }}">
                                        <i class="fas fa-star me-2" style="color: #F4C542;"></i> Give Feedback
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn-nav btn-nav-login" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn-nav btn-nav-register" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-graduation-cap me-2"></i> ScholarEase</h5>
                    <p class="mt-3" style="opacity: 0.8;">
                        Your intelligent SPM scholarship finder. Making education accessible for all Malaysian students.
                    </p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('dashboard') }}" class="text-light opacity-75 text-decoration-none">Dashboard</a></li>
                        <li class="mb-2"><a href="{{ route('scholarship.finder') }}" class="text-light opacity-75 text-decoration-none">Find Scholarships</a></li>
                        <li class="mb-2"><a href="{{ route('bookmarks.index') }}" class="text-light opacity-75 text-decoration-none">Bookmarks</a></li>
                        <li class="mb-2"><a href="{{ route('feedback.create') }}" class="text-light opacity-75 text-decoration-none">Give Feedback</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Contact</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> support@scholarease.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +603-1234 5678</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Kuala Lumpur, Malaysia</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center" style="opacity: 0.6;">
                &copy; {{ date('Y') }} ScholarEase. All rights reserved. | Making Education Accessible
            </div>
        </div>
    </footer>

    <!-- ============================================ -->
    <!-- SCRIPTS - URUTAN PENTING!                    -->
    <!-- ============================================ -->
    
    <!-- jQuery (required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 - PASTIKAN INI ADA! -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom Scripts from child views (finder.blade.php etc) -->
    @stack('scripts')
    
    <!-- Default initialization -->
    <script>
        AOS.init({ duration: 800, once: true });
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>