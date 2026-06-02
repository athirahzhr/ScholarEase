@extends('layouts.app')

@section('content')
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
    }

    .register-wrapper {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        padding: 4rem 1rem;
        position: relative;
        overflow: hidden;
    }

    .register-wrapper::before {
        content: '';
        position: absolute;
        top: -20%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(244,197,66,0.15) 0%, rgba(244,197,66,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .register-wrapper::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(122,0,25,0.08) 0%, rgba(122,0,25,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 32px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.5);
        overflow: hidden;
        transition: transform 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .register-card:hover {
        transform: translateY(-5px);
    }

    .register-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 2rem;
        text-align: center;
        border-bottom: 3px solid var(--gold);
    }

    .register-header h2 {
        color: white;
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .register-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0;
        font-size: 0.9rem;
    }

    .register-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
    }

    .form-label i {
        color: var(--maroon);
        margin-right: 8px;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
    }

    .form-control.is-invalid {
        border-color: #dc2626;
    }

    .invalid-feedback {
        color: #dc2626;
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: block;
    }

    .btn-register {
        width: 100%;
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        padding: 0.875rem;
        border: none;
        border-radius: 40px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.4);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }

    .login-prompt {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .login-prompt p {
        color: var(--gray-600);
        margin: 0;
        font-size: 0.9rem;
    }

    .login-prompt a {
        color: var(--maroon);
        font-weight: 700;
        text-decoration: none;
    }

    .login-prompt a:hover {
        color: var(--gold);
        text-decoration: underline;
    }

    .separator {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0;
    }

    .separator::before,
    .separator::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e5e7eb;
    }

    .separator span {
        padding: 0 1rem;
        color: #9ca3af;
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .register-card {
            margin: 0 1rem;
        }
        
        .register-header h2 {
            font-size: 1.5rem;
        }
        
        .register-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="register-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="register-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="register-header">
                        <h2>
                            <i class="fas fa-user-plus" style="margin-right: 10px;"></i>
                            Create Account
                        </h2>
                        <p>Join ScholarEase and find your perfect scholarship</p>
                    </div>

                    <div class="register-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Full Name
                                </label>
                                <input 
                                    id="name" 
                                    type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    autocomplete="name" 
                                    autofocus
                                    placeholder="Enter your full name"
                                >
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                </label>
                                <input 
                                    id="email" 
                                    type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autocomplete="email"
                                    placeholder="you@example.com"
                                >
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Password
                                </label>
                                <input 
                                    id="password" 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Create a password (min. 8 characters)"
                                >
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="form-label">
                                    <i class="fas fa-check-circle"></i>
                                    Confirm Password
                                </label>
                                <input 
                                    id="password-confirm" 
                                    type="password" 
                                    class="form-control" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Confirm your password"
                                >
                            </div>

                            <button type="submit" class="btn-register">
                                <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                                Create Account
                            </button>

                            <div class="separator">
                                <span>Already have an account?</span>
                            </div>

                            <div class="login-prompt">
                                <a href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i> Login to Your Account
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
@endpush
@endsection