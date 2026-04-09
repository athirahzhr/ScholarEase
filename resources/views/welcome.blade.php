<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="ScholarEase - Your Personalized Scholarship Finder Platform">

        <title>ScholarEase - Find Your Perfect Scholarship</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Styles -->
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Figtree', sans-serif;
                line-height: 1.6;
                color: #333;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }

            /* Header */
            header {
                padding: 2rem 0;
                background: rgba(255, 255, 255, 0.95);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                position: fixed;
                width: 100%;
                top: 0;
                z-index: 1000;
            }

            .nav-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 1.8rem;
                font-weight: 700;
                color: #4f46e5;
                text-decoration: none;
            }

            .logo i {
                color: #4f46e5;
                font-size: 2rem;
            }

            .nav-links {
                display: flex;
                gap: 2rem;
                align-items: center;
            }

            .nav-links a {
                color: #4b5563;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s;
            }

            .nav-links a:hover {
                color: #4f46e5;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-primary {
                background: #4f46e5;
                color: white; /* Changed to white */
                border: 2px solid #4f46e5;
            }

            .btn-primary:hover {
                background: #4338ca;
                color: white; /* Keeps white on hover */
                border-color: #4338ca;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            }

            .btn-secondary {
                background: transparent;
                color: #4f46e5;
                border: 2px solid #4f46e5;
            }

            .btn-secondary:hover {
                background: #4f46e5;
                color: white;
                transform: translateY(-2px);
            }

            /* Hero Section */
            .hero {
                padding: 10rem 0 6rem;
                background: linear-gradient(rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9)), 
                            url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
                background-size: cover;
                background-position: center;
            }

            .hero-content {
                text-align: center;
                max-width: 800px;
                margin: 0 auto;
            }

            .hero h1 {
                font-size: 3.5rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 1.5rem;
                line-height: 1.2;
            }

            .hero p {
                font-size: 1.25rem;
                color: #6b7280;
                margin-bottom: 2.5rem;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            /* Features Section */
            .features {
                padding: 6rem 0;
                background: #f9fafb;
            }

            .section-title {
                text-align: center;
                margin-bottom: 3rem;
            }

            .section-title h2 {
                font-size: 2.5rem;
                color: #1f2937;
                margin-bottom: 1rem;
            }

            .section-title p {
                color: #6b7280;
                max-width: 600px;
                margin: 0 auto;
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 2rem;
                margin-top: 3rem;
            }

            .feature-card {
                background: white;
                padding: 2rem;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s, box-shadow 0.3s;
                text-align: center;
            }

            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            }

            .feature-icon {
                width: 70px;
                height: 70px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem;
                color: white;
                font-size: 1.8rem;
            }

            .feature-card h3 {
                font-size: 1.5rem;
                color: #1f2937;
                margin-bottom: 1rem;
            }

            .feature-card p {
                color: #6b7280;
                line-height: 1.6;
            }

            /* How It Works */
            .how-it-works {
                padding: 6rem 0;
                background: white;
            }

            .steps {
                display: flex;
                justify-content: center;
                gap: 3rem;
                margin-top: 3rem;
                flex-wrap: wrap;
            }

            .step {
                text-align: center;
                max-width: 250px;
            }

            .step-number {
                width: 50px;
                height: 50px;
                background: #4f46e5;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0 auto 1.5rem;
            }

            .step h4 {
                font-size: 1.25rem;
                color: #1f2937;
                margin-bottom: 1rem;
            }

            /* CTA Section */
            .cta {
                padding: 6rem 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-align: center;
            }

            .cta h2 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }

            .cta p {
                font-size: 1.25rem;
                max-width: 600px;
                margin: 0 auto 2.5rem;
                opacity: 0.9;
            }

            /* Footer */
            footer {
                background: #1f2937;
                color: white;
                padding: 4rem 0 2rem;
            }

            .footer-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 3rem;
                margin-bottom: 3rem;
            }

            .footer-column h4 {
                font-size: 1.25rem;
                margin-bottom: 1.5rem;
                color: white;
            }

            .footer-column ul {
                list-style: none;
            }

            .footer-column ul li {
                margin-bottom: 0.75rem;
            }

            .footer-column ul li a {
                color: #d1d5db;
                text-decoration: none;
                transition: color 0.3s;
            }

            .footer-column ul li a:hover {
                color: white;
            }

            .copyright {
                text-align: center;
                padding-top: 2rem;
                border-top: 1px solid #374151;
                color: #9ca3af;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .hero h1 {
                    font-size: 2.5rem;
                }
                
                .nav-links {
                    display: none;
                }
                
                .mobile-menu-btn {
                    display: block;
                }
                
                .hero-buttons {
                    flex-direction: column;
                    align-items: center;
                }
                
                .steps {
                    flex-direction: column;
                    align-items: center;
                }
            }

            /* Mobile Menu Button */
            .mobile-menu-btn {
                display: none;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: #4f46e5;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <!-- Header -->
        <header>
            <div class="container nav-container">
                <a href="/" class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>ScholarEase</span>
                </a>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav class="nav-links" id="navLinks">
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#about">About</a>
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-secondary">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Get Started
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container hero-content">
                <h1>Find Your Perfect Scholarship Match</h1>
                <p>ScholarEase uses AI-powered matching to connect students with scholarships based on their academic profile, financial background, and study preferences.</p>
                <div class="hero-buttons">
                    @auth
                        <a href="{{ route('scholarship.finder') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Find Scholarships
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-user-circle"></i> My Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-rocket"></i> Start Free Trial
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> Login to Account
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="container">
                <div class="section-title">
                    <h2>Why Choose ScholarEase?</h2>
                    <p>Our platform is designed to make scholarship discovery simple, personalized, and effective.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3>AI-Powered Matching</h3>
                        <p>Advanced algorithms match you with scholarships that fit your unique profile and qualifications.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3>SPM Results Analysis</h3>
                        <p>Upload your SPM results and get instant scholarship recommendations based on your grades.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-filter"></i>
                        </div>
                        <h3>Smart Filters</h3>
                        <p>Filter scholarships by academic category, income level, study path, and application deadline.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Deadline Alerts</h3>
                        <p>Never miss an application deadline with our smart notification system.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <h3>Save & Organize</h3>
                        <p>Bookmark scholarships and track your applications in one convenient dashboard.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Success Tracking</h3>
                        <p>Monitor your scholarship application progress and improve your success rate.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works" id="how-it-works">
            <div class="container">
                <div class="section-title">
                    <h2>How It Works</h2>
                    <p>Get personalized scholarship recommendations in just 3 simple steps</p>
                </div>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h4>Create Profile</h4>
                        <p>Sign up and complete your academic and financial profile</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h4>Upload Results</h4>
                        <p>Upload your SPM results using our OCR technology</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h4>Get Matches</h4>
                        <p>Receive personalized scholarship recommendations instantly</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta" id="about">
            <div class="container">
                <h2>Ready to Find Your Scholarship Match?</h2>
                <p>Join thousands of students who have found their perfect scholarship through ScholarEase. Start your journey today!</p>
                @auth
                    <a href="{{ route('scholarship.finder') }}" class="btn btn-primary" style="background: white; color: #4f46e5; border: 2px solid white;">
                        <i class="fas fa-search"></i> Find Scholarships Now
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary" style="background: white; color: #4f46e5; border: 2px solid white;">
                        <i class="fas fa-user-plus"></i> Sign Up 
                    </a>
                @endauth
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-column">
                        <h4>ScholarEase</h4>
                        <p>Your personalized scholarship finder platform. Making education accessible through smart technology.</p>
                    </div>
                    <div class="footer-column">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="/">Home</a></li>
                            <li><a href="#features">Features</a></li>
                            <li><a href="#how-it-works">How It Works</a></li>
                            <li><a href="#about">About</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Platform</h4>
                        <ul>
                            @auth
                                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li><a href="{{ route('scholarship.finder') }}">Scholarship Finder</a></li>
                                <li><a href="{{ route('bookmarks.index') }}">My Bookmarks</a></li>
                                <li><a href="{{ route('scholarships.browse') }}">Browse Scholarships</a></li>
                            @else
                                <li><a href="{{ route('login') }}">Login</a></li>
                                <li><a href="{{ route('register') }}">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Contact</h4>
                        <ul>
                            <li><i class="fas fa-envelope"></i> support@scholarease.com</li>
                            <li><i class="fas fa-phone"></i> +60 3-1234 5678</li>
                            <li><i class="fas fa-map-marker-alt"></i> Kuala Lumpur, Malaysia</li>
                        </ul>
                    </div>
                </div>
                <div class="copyright">
                    <p>&copy; {{ date('Y') }} ScholarEase. All rights reserved. | Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
                </div>
            </div>
        </footer>

        <!-- JavaScript -->
        <script>
            // Mobile menu toggle
            document.getElementById('mobileMenuBtn').addEventListener('click', function() {
                const navLinks = document.getElementById('navLinks');
                navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if(targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if(targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Update mobile menu display on resize
            window.addEventListener('resize', function() {
                const navLinks = document.getElementById('navLinks');
                if(window.innerWidth > 768) {
                    navLinks.style.display = 'flex';
                } else {
                    navLinks.style.display = 'none';
                }
            });

            // Initialize mobile menu state
            window.addEventListener('load', function() {
                if(window.innerWidth <= 768) {
                    document.getElementById('navLinks').style.display = 'none';
                    document.getElementById('mobileMenuBtn').style.display = 'block';
                }
            });
        </script>
    </body>
</html>