<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ScholarEase - @yield('title', 'SPM Scholarship Finder')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- ============================================ -->
    <!-- BOOTSTRAP 5 CSS - SIMPLE RELIABLE CDN        -->
    <!-- ============================================ -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    
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
            min-height: 100vh;
        }
        
        /* ============================================ */
        /* NAVBAR STYLES                                */
        /* ============================================ */
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
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.02);
            opacity: 0.9;
        }
        
        .navbar-brand i {
            background: linear-gradient(135deg, var(--maroon), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
        
        .nav-link.active {
            color: var(--maroon) !important;
            font-weight: 700;
        }
        
        .nav-link.active::after {
            width: 80%;
        }
        
        /* ============================================ */
        /* BUTTON STYLES                               */
        /* ============================================ */
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
            box-shadow: 0 4px 12px rgba(122, 0, 25, 0.2);
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
        
        .btn-outline-secondary {
            border: 2px solid var(--gray-600);
            color: var(--gray-600);
            background: transparent;
            border-radius: 60px;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background: var(--gray-600);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: linear-gradient(115deg, #ef4444, #dc2626);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 60px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
            background: linear-gradient(115deg, #dc2626, #b91c1c);
            color: white;
        }
        
        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            min-height: 36px;
        }
        
        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.05rem;
            min-height: 52px;
        }
        
        /* ============================================ */
        /* CARD STYLES                                 */
        /* ============================================ */
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
        
        /* ============================================ */
        /* ALERT STYLES                                */
        /* ============================================ */
        .alert {
            border-radius: 16px;
            border: none;
        }
        
        .alert-info {
            background: linear-gradient(135deg, var(--cream), var(--cream-dark));
            color: var(--maroon);
            border-left: 4px solid var(--gold);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }
        
        /* ============================================ */
        /* FOOTER STYLES                               */
        /* ============================================ */
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
            text-decoration: none;
        }
        
        footer a:hover {
            color: var(--gold) !important;
            padding-left: 5px;
        }
        
        /* ============================================ */
        /* DROPDOWN STYLES                             */
        /* ============================================ */
        .dropdown-menu {
            border-radius: 16px;
            border: 1px solid rgba(122, 0, 25, 0.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
        }
        
        .dropdown-item {
            border-radius: 10px;
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: rgba(122, 0, 25, 0.05);
            color: var(--maroon);
        }
        
        .dropdown-item.text-danger:hover {
            background: rgba(220, 38, 38, 0.1);
        }
        
        /* ============================================ */
        /* FORM STYLES                                 */
        /* ============================================ */
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
            outline: none;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--gray-800);
        }
        
        /* ============================================ */
        /* TABLE STYLES                                */
        /* ============================================ */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table th {
            background: linear-gradient(135deg, var(--cream), var(--cream-dark));
            color: var(--maroon);
            font-weight: 700;
            border-bottom: 2px solid var(--gold);
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
        
        /* ============================================ */
        /* UTILITY CLASSES                             */
        /* ============================================ */
        .text-maroon {
            color: var(--maroon);
        }
        
        .bg-maroon {
            background: var(--maroon);
        }
        
        .bg-maroon-dark {
            background: var(--maroon-dark);
        }
        
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 1rem; }
        .gap-4 { gap: 1.5rem; }
        
        .flex-1 { flex: 1; }
        
        /* ============================================ */
        /* RESPONSIVE                                  */
        /* ============================================ */
        @media (max-width: 992px) {
            .navbar-brand {
                font-size: 1.5rem;
            }
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
                padding: 0.5rem 0;
            }
            
            .navbar-nav .nav-item {
                width: 100%;
            }
            
            .navbar-nav .nav-link {
                padding: 0.6rem 1rem;
                border-radius: 10px;
            }
            
            .dropdown-menu {
                border: none;
                box-shadow: none;
                background: transparent;
                padding-left: 1rem;
            }
            
            .dropdown-item {
                padding: 0.5rem 1rem;
            }
            
            footer {
                text-align: center;
            }
            
            footer .col-md-3, footer .col-md-6 {
                margin-bottom: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .btn-nav {
                font-size: 0.85rem;
                padding: 0.4rem 1rem;
            }
        }
        
        /* ============================================ */
        /* NAVBAR TOGGLER                              */
        /* ============================================ */
        .navbar-toggler {
            border: none;
            background: transparent;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: none;
            position: relative;
        }
        
        .navbar-toggler-icon i {
            font-size: 1.5rem;
            color: var(--maroon);
        }
        
        /* ============================================ */
        /* ANIMATIONS                                  */
        /* ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        /* ============================================ */
        /* MAIN CONTENT MIN HEIGHT                     */
        /* ============================================ */
        main {
            min-height: calc(100vh - 300px);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- ============================================ -->
    <!-- NAVIGATION                                   -->
    <!-- ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-scholarease">
        <div class="container">
            <!-- ============================================ -->
            <!-- FIXED: ScholarEase Logo - Now links to welcome page -->
            <!-- ============================================ -->
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="fas fa-graduation-cap me-2"></i> ScholarEase
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">
                    <i class="fas fa-bars"></i>
                </span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('scholarship.finder') ? 'active' : '' }}" href="{{ route('scholarship.finder') }}">
                                <i class="fas fa-search me-1"></i> Find
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}" href="{{ route('bookmarks.index') }}">
                                <i class="fas fa-bookmark me-1"></i> Bookmarks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.create') }}">
                                <i class="fas fa-star me-1"></i> Feedback
                            </a>
                        </li>
                        
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-crown me-1"></i> Admin
                                </a>
                            </li>
                        @endif
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
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

    <!-- ============================================ -->
    <!-- MAIN CONTENT                                -->
    <!-- ============================================ -->
    <main>
        @yield('content')
    </main>

    <!-- ============================================ -->
    <!-- FOOTER                                      -->
    <!-- ============================================ -->
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
                        <li class="mb-2"><a href="{{ route('welcome') }}" class="text-light opacity-75">Home</a></li>
                        <li class="mb-2"><a href="{{ route('dashboard') }}" class="text-light opacity-75">Dashboard</a></li>
                        <li class="mb-2"><a href="{{ route('scholarship.finder') }}" class="text-light opacity-75">Find Scholarships</a></li>
                        <li class="mb-2"><a href="{{ route('feedback.create') }}" class="text-light opacity-75">Give Feedback</a></li>
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
    
    <!-- 1. jQuery (required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- 2. Bootstrap JS - Simple reliable CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- 3. SweetAlert2 - Multiple CDN fallback -->
    <script>
        // Force load SweetAlert2 with multiple CDN fallbacks
        (function loadSweetAlert() {
            if (typeof Swal !== 'undefined' && Swal) {
                console.log('✅ SweetAlert2 already loaded');
                return;
            }
            
            console.log('⏳ Loading SweetAlert2...');
            
            var cdnUrls = [
                'https://cdn.jsdelivr.net/npm/sweetalert2@11',
                'https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.all.min.js',
                'https://unpkg.com/sweetalert2@11/dist/sweetalert2.all.min.js'
            ];
            
            var loaded = false;
            
            function tryLoad(index) {
                if (loaded || index >= cdnUrls.length) {
                    if (!loaded) {
                        console.warn('⚠️ All SweetAlert2 CDN failed, using native fallback');
                        window.Swal = null;
                    }
                    return;
                }
                
                var script = document.createElement('script');
                script.src = cdnUrls[index];
                script.async = false;
                
                script.onload = function() {
                    if (typeof Swal !== 'undefined' && Swal) {
                        loaded = true;
                        console.log('✅ SweetAlert2 loaded from:', cdnUrls[index]);
                    } else {
                        tryLoad(index + 1);
                    }
                };
                
                script.onerror = function() {
                    console.warn('❌ Failed to load from:', cdnUrls[index]);
                    tryLoad(index + 1);
                };
                
                document.head.appendChild(script);
            }
            
            // Start loading from first CDN
            tryLoad(0);
            
            // Final fallback: after 5 seconds, if still not loaded
            setTimeout(function() {
                if (typeof Swal === 'undefined' || !Swal) {
                    console.warn('⚠️ SweetAlert2 timeout, using native fallback');
                    window.Swal = null;
                }
            }, 5000);
        })();
    </script>
    
    <!-- 4. AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- 5. Custom Scripts from child views -->
    @stack('scripts')
    
    <!-- 6. Default initialization -->
    <script>
        // Initialize AOS
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 800, once: true });
        }
        
        // CSRF Token for AJAX requests
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
        
        // Initialize tooltips
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
        // Close mobile menu on link click
        document.addEventListener('DOMContentLoaded', function() {
            var navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            var navbarCollapse = document.getElementById('navbarNav');
            
            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (navbarCollapse.classList.contains('show')) {
                        var bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>