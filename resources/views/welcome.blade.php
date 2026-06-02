<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="ScholarEase - Your Personalized Scholarship Finder Platform">

        <title>ScholarEase | Smart Scholarship Matching Platform</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- AOS Animation Library -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --maroon: #7A0019;
                --maroon-dark: #4e0010;
                --maroon-soft: #9e1e32;
                --gold: #F4C542;
                --gold-light: #ffda77;
                --cream: #FFF8EE;
                --cream-dark: #f5ebe0;
                --gray-800: #1f2937;
                --gray-600: #4b5563;
                --shadow-sm: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.02);
                --shadow-md: 0 20px 25px -12px rgba(0,0,0,0.1);
                --shadow-lg: 0 25px 30px -12px rgba(0,0,0,0.15);
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--cream);
                color: var(--gray-800);
                overflow-x: hidden;
            }

            .container {
                max-width: 1280px;
                margin: 0 auto;
                padding: 0 32px;
            }

            /* Glassmorphic Header */
            header {
                padding: 1rem 0;
                position: fixed;
                width: 100%;
                top: 0;
                z-index: 1000;
                backdrop-filter: blur(20px);
                background: rgba(255, 248, 238, 0.85);
                border-bottom: 1px solid rgba(122, 0, 25, 0.1);
                transition: all 0.3s ease;
            }

            .nav-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 1.8rem;
                font-weight: 800;
                background: linear-gradient(135deg, var(--maroon) 0%, var(--gold) 100%);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                text-decoration: none;
                letter-spacing: -0.5px;
            }

            .logo i {
                background: linear-gradient(135deg, var(--maroon), var(--gold));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                font-size: 2rem;
            }

            .nav-links {
                display: flex;
                gap: 2.5rem;
                align-items: center;
            }

            .nav-links a {
                color: var(--gray-600);
                text-decoration: none;
                font-weight: 500;
                transition: all 0.2s ease;
                position: relative;
            }

            .nav-links a:hover {
                color: var(--maroon);
            }

            .nav-links a::after {
                content: '';
                position: absolute;
                bottom: -6px;
                left: 0;
                width: 0%;
                height: 2px;
                background: var(--gold);
                transition: width 0.3s ease;
            }

            .nav-links a:hover::after {
                width: 100%;
            }

            .btn {
                padding: 0.7rem 1.6rem;
                border-radius: 60px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-size: 0.95rem;
                letter-spacing: -0.2px;
            }

            /* BRIGHTER GET STARTED BUTTON */
            .btn-primary {
                background: linear-gradient(115deg, #ffb347, #ff8c00);
                color: #2c1a00;
                box-shadow: 0 8px 20px rgba(255, 140, 0, 0.4);
                border: none;
                font-weight: 700;
            }

            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 30px rgba(255, 140, 0, 0.5);
                background: linear-gradient(115deg, #ffa726, #ff9800);
                color: #1f1400;
            }

            .btn-secondary {
                background: rgba(122,0,25,0.08);
                color: var(--maroon);
                border: 1px solid rgba(122,0,25,0.2);
                backdrop-filter: blur(4px);
            }

            .btn-secondary:hover {
                background: var(--maroon);
                color: white;
                border-color: transparent;
                transform: translateY(-3px);
            }

            .btn-gold {
                background: linear-gradient(135deg, #FFD966, #FFB347);
                color: #2c1a00;
                box-shadow: 0 8px 20px rgba(255, 180, 60, 0.5);
                font-weight: 800;
            }

            .btn-gold:hover {
                background: linear-gradient(135deg, #FFE084, #FFC107);
                transform: translateY(-3px);
                box-shadow: 0 12px 28px rgba(255, 190, 70, 0.6);
            }

            /* Hero Section */
            .hero {
                padding: 10rem 0 6rem;
                position: relative;
                overflow: hidden;
                background: radial-gradient(circle at 10% 20%, rgba(255,248,238,1) 0%, rgba(245,235,224,1) 100%);
            }

            .hero::before {
                content: '';
                position: absolute;
                top: -30%;
                right: -20%;
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(244,197,66,0.2) 0%, rgba(244,197,66,0) 70%);
                border-radius: 50%;
                pointer-events: none;
            }

            .hero::after {
                content: '';
                position: absolute;
                bottom: -20%;
                left: -10%;
                width: 500px;
                height: 500px;
                background: radial-gradient(circle, rgba(122,0,25,0.1) 0%, rgba(122,0,25,0) 70%);
                border-radius: 50%;
                pointer-events: none;
            }

            .hero-content {
                text-align: center;
                max-width: 900px;
                margin: 0 auto;
                position: relative;
                z-index: 2;
            }

            .hero-badge {
                display: inline-block;
                background: rgba(122,0,25,0.1);
                backdrop-filter: blur(4px);
                padding: 0.4rem 1.2rem;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--maroon);
                margin-bottom: 1.5rem;
                border: 1px solid rgba(122,0,25,0.2);
            }

            .hero h1 {
                font-size: 4rem;
                font-weight: 800;
                background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 50%, var(--gold) 100%);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                margin-bottom: 1.5rem;
                line-height: 1.2;
                letter-spacing: -1.5px;
            }

            .hero p {
                font-size: 1.25rem;
                color: var(--gray-600);
                max-width: 650px;
                margin: 0 auto 2.5rem;
                line-height: 1.6;
            }

            /* Stats Cards */
            .stats-section {
                margin-top: -50px;
                position: relative;
                z-index: 10;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 24px;
            }

            .stat-card {
                background: rgba(255,255,255,0.9);
                backdrop-filter: blur(12px);
                border-radius: 32px;
                padding: 28px 20px;
                text-align: center;
                box-shadow: var(--shadow-md);
                border: 1px solid rgba(255,255,255,0.5);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-8px);
                background: white;
                border-color: var(--gold);
            }

            .stat-card h2 {
                font-size: 2.8rem;
                font-weight: 800;
                background: linear-gradient(135deg, var(--maroon), var(--gold));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                margin-bottom: 8px;
            }

            .stat-card p {
                color: var(--gray-600);
                font-weight: 500;
            }

            /* Feature Cards */
            .features {
                padding: 6rem 0;
            }

            .section-title {
                text-align: center;
                margin-bottom: 4rem;
            }

            .section-title h2 {
                font-size: 2.8rem;
                font-weight: 700;
                letter-spacing: -0.02em;
                background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                margin-bottom: 1rem;
            }

            .section-title p {
                color: var(--gray-600);
                max-width: 600px;
                margin: 0 auto;
                font-size: 1.1rem;
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
                gap: 2rem;
            }

            .feature-card {
                background: white;
                padding: 2rem;
                border-radius: 32px;
                transition: all 0.4s ease;
                border: 1px solid rgba(0,0,0,0.05);
                position: relative;
                overflow: hidden;
                box-shadow: var(--shadow-sm);
            }

            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 6px;
                background: linear-gradient(90deg, var(--gold), var(--maroon));
                transform: scaleX(0);
                transform-origin: left;
                transition: transform 0.4s ease;
            }

            .feature-card:hover::before {
                transform: scaleX(1);
            }

            .feature-card:hover {
                transform: translateY(-8px);
                box-shadow: var(--shadow-lg);
                border-color: rgba(244,197,66,0.3);
            }

            .feature-icon {
                width: 70px;
                height: 70px;
                background: linear-gradient(135deg, rgba(122,0,25,0.08), rgba(244,197,66,0.15));
                border-radius: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1.5rem;
            }

            .feature-icon i {
                font-size: 2.2rem;
                background: linear-gradient(135deg, var(--maroon), var(--gold));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            .feature-card h3 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
                font-weight: 700;
            }

            /* Scholarship cards */
            .scholarship-card {
                background: white;
                border-radius: 28px;
                padding: 1.5rem;
                transition: all 0.3s;
                border: 1px solid rgba(0,0,0,0.05);
                box-shadow: var(--shadow-sm);
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .scholarship-card:hover {
                transform: translateY(-6px);
                box-shadow: var(--shadow-md);
                border-color: var(--gold);
            }

            .apply-btn {
                background: linear-gradient(95deg, #F4C542, #E6B13E);
                border: none;
                padding: 8px 18px;
                border-radius: 40px;
                font-weight: 700;
                font-size: 0.8rem;
                color: #2c1a00;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                text-decoration: none;
            }

            .apply-btn:hover {
                background: linear-gradient(95deg, #FFD966, #F4C542);
                transform: scale(1.02);
                box-shadow: 0 6px 14px rgba(244,197,66,0.4);
            }

            /* Steps Section */
            .how-it-works {
                padding: 6rem 0;
                background: linear-gradient(120deg, #fff, var(--cream-dark));
            }

            .steps {
                display: flex;
                justify-content: center;
                gap: 4rem;
                margin-top: 3rem;
                flex-wrap: wrap;
            }

            .step {
                text-align: center;
                max-width: 260px;
                background: white;
                padding: 2rem 1.5rem;
                border-radius: 48px;
                box-shadow: var(--shadow-sm);
                transition: all 0.3s;
            }

            .step:hover {
                transform: translateY(-10px);
                box-shadow: var(--shadow-md);
            }

            .step-number {
                width: 64px;
                height: 64px;
                background: linear-gradient(135deg, var(--gold), var(--gold-light));
                color: var(--maroon-dark);
                border-radius: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                font-weight: 800;
                margin: 0 auto 1.5rem;
            }


            .cta {
                padding: 6rem 0;
                background: linear-gradient(115deg, var(--maroon) 0%, var(--maroon-dark) 100%);
                position: relative;
                overflow: hidden;
            }

            .cta::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 100%;
                background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,248,238,0.05)" fill-opacity="0.3" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
                background-repeat: no-repeat;
                background-position: bottom;
                background-size: cover;
                opacity: 0.2;
            }

            .cta h2 {
                background: linear-gradient(125deg, #FFE6B0, #FFD966, #FFB347);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                text-shadow: 0 2px 15px rgba(255,200,80,0.3);
                font-size: 2.8rem;
                font-weight: 800;
                margin-bottom: 1rem;
            }

            .cta p {
                color: #f8f3e4;
                font-size: 1.25rem;
                text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                font-weight: 500;
                background: rgba(0,0,0,0.2);
                display: inline-block;
                padding: 0.2rem 1rem;
                border-radius: 40px;
                backdrop-filter: blur(2px);
            }

            /* Footer */
            footer {
                background: var(--maroon-dark);
                color: white;
                padding: 4rem 0 2rem;
            }

            .footer-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 3rem;
                margin-bottom: 3rem;
            }

            .footer-column h4 {
                font-size: 1.2rem;
                margin-bottom: 1.5rem;
                position: relative;
                display: inline-block;
            }

            .footer-column ul li {
                margin-bottom: 0.75rem;
                list-style: none;
            }

            .footer-column ul li a {
                color: #d1d5db;
                text-decoration: none;
                transition: 0.2s;
            }

            .footer-column ul li a:hover {
                color: var(--gold);
                padding-left: 5px;
            }

            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .container {
                    padding: 0 20px;
                }
                .hero h1 {
                    font-size: 2.5rem;
                }
                .nav-links {
                    display: none;
                    flex-direction: column;
                    position: absolute;
                    top: 70px;
                    left: 0;
                    right: 0;
                    background: rgba(255,248,238,0.98);
                    backdrop-filter: blur(16px);
                    padding: 2rem;
                    gap: 1.5rem;
                    border-radius: 0 0 32px 32px;
                    box-shadow: var(--shadow-md);
                }
                .nav-links.show {
                    display: flex;
                }
                .mobile-menu-btn {
                    display: block;
                    background: none;
                    border: none;
                    font-size: 1.8rem;
                    color: var(--maroon);
                    cursor: pointer;
                }
                .stats-grid {
                    grid-template-columns: 1fr 1fr;
                    gap: 16px;
                }
                .steps {
                    flex-direction: column;
                    align-items: center;
                }
            }

            @media (min-width: 769px) {
                .mobile-menu-btn {
                    display: none;
                }
            }

            .toast-notify {
                position: fixed;
                bottom: 30px;
                left: 50%;
                transform: translateX(-50%);
                background: #1e293b;
                color: #FFD966;
                padding: 12px 24px;
                border-radius: 60px;
                font-weight: 600;
                z-index: 2000;
                backdrop-filter: blur(12px);
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                font-size: 0.9rem;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s ease;
            }

            .testimonial-section{
                padding:80px 0;
                background:#ffffff;
            }

            .testimonial-grid{
                display:grid;
                grid-template-columns:
                repeat(auto-fit,minmax(300px,1fr));
                gap:25px;
            }

            .testimonial-card{
                background:#fff8ee;
                border-radius:24px;
                padding:25px;
                border-left:5px solid #F4C542;
                box-shadow:0 8px 20px rgba(0,0,0,.08);
            }

            .testimonial-card p{
                margin:15px 0;
                line-height:1.6;
            }

            .testimonial-card h4{
                color:#7A0019;
            }

            .stars{
                font-size:1.2rem;
            }
        </style>
    </head>
    <body>

        <header id="header">
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
                        <a href="{{ url('/home') }}" class="btn btn-secondary"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> Get Started</a>
                    @endauth
                </nav>
            </div>
        </header>

        <section class="hero">
            <div class="container hero-content" data-aos="fade-up" data-aos-duration="800">
                <div class="hero-badge">
                    <i class="fas fa-robot"></i> Scholarship Recommendation System
                </div>
                <h1>Your Future Starts <br>With the Perfect Scholarship</h1>
                <p>ScholarEase helps students discover scholarships that match their academic achievements, financial background, and study preferences through an intelligent recommendation system.</p>
                <div class="hero-buttons">
                    @auth
                        <a href="{{ route('scholarship.finder') }}" class="btn btn-primary"><i class="fas fa-search"></i> Find Scholarships</a>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fas fa-user-circle"></i> My Dashboard</a>
                    @endauth
                </div>
            </div>
        </section>

        <section class="stats-section">
            <div class="container">
                <div class="stats-grid" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card"><h2>500+</h2><p>Scholarships</p></div>
                    <div class="stat-card"><h2>1000+</h2><p>Students</p></div>
                    <div class="stat-card"><h2>95%</h2><p>Match Accuracy</p></div>
                    <div class="stat-card"><h2>24/7</h2><p>Support</p></div>
                </div>
            </div>
        </section>

        <section class="features" id="features">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Why Choose ScholarEase?</h2>
                    <p>Smart technology designed to unlock education funding opportunities</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="50"><div class="feature-icon"><i class="fas fa-robot"></i></div><h3>Smart Recommendation Engine</h3><p>Automatically identifies scholarships that match your academic qualifications, financial background, and study interests.</p></div>
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="100"><div class="feature-icon"><i class="fas fa-file-invoice-dollar"></i></div><h3>SPM Results Analysis</h3><p>Upload your SPM results and get instant recommendations based on grades.</p></div>
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="150"><div class="feature-icon"><i class="fas fa-filter"></i></div><h3>Smart Filters</h3><p>Filter by category, income, study path, and deadlines effortlessly.</p></div>
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="200"><div class="feature-icon"><i class="fas fa-bell"></i></div><h3>Deadline Alerts</h3><p>Never miss an application deadline with smart reminders.</p></div>
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="250"><div class="feature-icon"><i class="fas fa-bookmark"></i></div><h3>Save & Organize</h3><p>Bookmark scholarships and track applications in one dashboard.</p></div>
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="300"><div class="feature-icon"><i class="fas fa-chart-line"></i></div><h3>Success Tracking</h3><p>Monitor progress and improve your success rate with insights.</p></div>
                </div>
            </div>
        </section>

        
        <!-- Latest Scholarships Section - Only show active scholarships (future deadline or no deadline) -->
<section class="features" style="background: white; padding-top: 0;">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Active Scholarships</h2>
            <p>Discover opportunities waiting for you - apply before deadlines!</p>
        </div>
        <div class="row mt-4" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px,1fr)); gap: 1.8rem;">
            @php
                $today = \Carbon\Carbon::today();
                $activeScholarships = $scholarships->filter(function($scholarship) use ($today) {
                    // Show if:
                    // 1. No deadline set (NULL), OR
                    // 2. Deadline is in the future (>= today)
                    return is_null($scholarship->deadline) || \Carbon\Carbon::parse($scholarship->deadline)->greaterThanOrEqualTo($today);
                });
            @endphp

            @forelse($activeScholarships as $scholarship)
                <div class="scholarship-card" data-aos="fade-up" data-id="{{ $scholarship->id }}" style="display: flex; flex-direction: column; height: 100%;">
                    <div style="flex: 1; display: flex; flex-direction: column;">
                        <h5 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 0.75rem;">{{ $scholarship->title }}</h5>
                        <p style="color: var(--maroon); font-weight: 600; margin-bottom: 0.75rem;">
                            <i class="fas fa-building"></i> {{ $scholarship->provider }}
                        </p>
                        
                        @if($scholarship->description)
                            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.5;">
                                {{ Str::limit($scholarship->description, 100) }}
                            </p>
                        @else
                            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1rem; font-style: italic;">
                                No description available
                            </p>
                        @endif
                    </div>
                    
                    <!-- Deadline section - always at bottom before button -->
                    <div style="margin-top: auto;">
                        <p style="color: #6b7280; margin-bottom: 1rem;">
                            <i class="fas fa-calendar-alt"></i> 
                            Deadline: 
                            @if($scholarship->deadline)
                                <span class="{{ \Carbon\Carbon::parse($scholarship->deadline)->isToday() ? 'text-warning' : '' }}">
                                    {{ \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y') }}
                                    @if(\Carbon\Carbon::parse($scholarship->deadline)->isToday())
                                        <span style="background: #ff9800; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem; margin-left: 8px;">Last Day!</span>
                                    @endif
                                </span>
                            @else
                                <span style="color: #27ae60;">Rolling / No Deadline</span>
                            @endif
                        </p>
                        
                        <!-- Apply Now button - always at the very bottom and aligned -->
                        <div style="margin-top: 0.5rem;">
                            @if($scholarship->application_link && $scholarship->application_link != '')
                                <a href="{{ $scholarship->application_link }}" 
                                   class="apply-btn" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   data-title="{{ $scholarship->title }}"
                                   style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(95deg, #F4C542, #E6B13E); border: none; padding: 10px 20px; border-radius: 40px; font-weight: 700; font-size: 0.85rem; color: #2c1a00; cursor: pointer; transition: all 0.2s ease; text-decoration: none; width: auto;">
                                    <i class="fas fa-external-link-alt"></i> Apply Now →
                                </a>
                            @else
                                <button class="apply-btn" disabled style="opacity: 0.6; cursor: not-allowed; display: inline-flex; align-items: center; gap: 8px; background: #ccc; border: none; padding: 10px 20px; border-radius: 40px; font-weight: 700; font-size: 0.85rem; color: #666;">
                                    <i class="fas fa-info-circle"></i> Apply Unavailable
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info" style="grid-column:1/-1; text-align:center; padding:2rem; background:#f1f5f9; border-radius:32px;">
                    <i class="fas fa-calendar-check" style="font-size: 2rem; display: block; margin-bottom: 1rem; color: var(--maroon);"></i>
                    ✨ No active scholarships at the moment. Check back soon for new opportunities!
                </div>
            @endforelse
        </div>
        
    </div>
</section>

        <section class="how-it-works" id="how-it-works">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>How It Works</h2>
                    <p>Three simple steps to unlock your education funding</p>
                </div>
                <div class="steps">
                    <div class="step" data-aos="flip-left" data-aos-delay="100"><div class="step-number">1</div><h4>Create Profile</h4><p>Sign up and complete your academic and financial profile.</p></div>
                    <div class="step" data-aos="flip-left" data-aos-delay="200"><div class="step-number">2</div><h4>Upload Results</h4><p>Upload your SPM results using our smart OCR technology.</p></div>
                    <div class="step" data-aos="flip-left" data-aos-delay="300"><div class="step-number">3</div><h4>Get Matches</h4><p>Receive personalized scholarship recommendations instantly.</p></div>
                </div>
            </div>
        </section>

        <!-- CTA with bright vivid text -->
        <section class="cta" id="about">
            <div class="container" style="text-align: center; position: relative; z-index: 2;" data-aos="zoom-in">
                <h2>🚀 Ready to Find Your Match?</h2>
                <p>✨ Join thousands of students who found funding through ScholarEase. ✨</p>
                <div style="margin-top: 2rem;">
                    @auth
                        <a href="{{ route('scholarship.finder') }}" class="btn btn-gold" style="font-weight: 800; padding: 0.9rem 2rem;"><i class="fas fa-search"></i> Explore Scholarships</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-gold" style="font-weight: 800; padding: 0.9rem 2rem;"><i class="fas fa-user-plus"></i> Sign Up →</a>
                    @endauth
                </div>
            </div>
        </section>

        <section class="testimonial-section">
        <div class="container">
        <div class="section-title">
        <h2>Student Feedback</h2>
        <p>
        What students say about ScholarEase
        </p>
        </div>

        <div class="testimonial-grid">

        @forelse($feedbacks as $feedback)

        <div class="testimonial-card">

        <div class="stars">

        {{ str_repeat('⭐', $feedback->rating) }}

        </div>

        <p>

        "{{ $feedback->comment }}"

        </p>

        <h4>

        {{ $feedback->user->name }}

        </h4>

        </div>

        @empty

        <div class="testimonial-card">

        <p>
        No feedback available yet.
        </p>

        </div>

        @endforelse

        </div>

        </div>

        </section>

        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-column"><h4>ScholarEase</h4><p style="opacity:0.8;">Smart scholarship matching platform for ambitious students.</p></div>
                    <div class="footer-column"><h4>Quick Links</h4><ul><li><a href="/">Home</a></li><li><a href="#features">Features</a></li><li><a href="#how-it-works">How It Works</a></li></ul></div>
                    <div class="footer-column"><h4>Platform</h4><ul>@auth<li><a href="{{ route('dashboard') }}">Dashboard</a></li><li><a href="{{ route('scholarship.finder') }}">Finder</a></li>@else<li><a href="{{ route('login') }}">Login</a></li><li><a href="{{ route('register') }}">Register</a></li>@endauth</ul></div>
                    <div class="footer-column"><h4>Contact</h4><ul><li><i class="fas fa-envelope"></i> support@scholarease.com</li><li><i class="fas fa-map-marker-alt"></i> Kuala Lumpur, Malaysia</li></ul></div>
                </div>
                <div class="copyright"><p>&copy; {{ date('Y') }} ScholarEase — Empowering education. All rights reserved. Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p></div>
            </div>
        </footer>

        <div id="applyToast" class="toast-notify"></div>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({ duration: 800, once: true, offset: 100 });
            
            const mobileBtn = document.getElementById('mobileMenuBtn');
            const navLinks = document.getElementById('navLinks');
            if(mobileBtn) {
                mobileBtn.addEventListener('click', () => {
                    navLinks.classList.toggle('show');
                });
            }
            
            window.addEventListener('resize', () => {
                if(window.innerWidth > 768) navLinks.classList.remove('show');
            });
            
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const hash = this.getAttribute('href');
                    if(hash === '#') return;
                    const target = document.querySelector(hash);
                    if(target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Toast notification for disabled apply buttons (optional)
            const toast = document.getElementById('applyToast');
            function showMessage(msg) {
                toast.textContent = msg;
                toast.style.opacity = '1';
                setTimeout(() => {
                    toast.style.opacity = '0';
                }, 2500);
            }

            // Handle disabled buttons (optional feedback)
            document.querySelectorAll('.apply-btn[disabled]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    showMessage('ℹ️ Application link not yet available for this scholarship.');
                });
            });
        </script>
    </body>
</html>