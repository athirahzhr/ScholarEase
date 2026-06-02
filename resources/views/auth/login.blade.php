@extends('layouts.app')

@section('content')
<style>
    :root {
        --maroon: #7A0019;
        --maroon-dark: #4e0010;
        --gold: #F4C542;
        --gold-light: #ffda77;
        --cream: #FFF8EE;
        --cream-dark: #f5ebe0;
        --gray-800: #1f2937;
        --gray-600: #4b5563;
    }

    .login-wrapper {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        padding: 4rem 1rem;
        position: relative;
        overflow: hidden;
    }

    .login-wrapper::before {
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

    .login-wrapper::after {
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

    .login-card {
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

    .login-card:hover {
        transform: translateY(-5px);
    }

    .login-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 2rem;
        text-align: center;
        border-bottom: 3px solid var(--gold);
    }

    .login-header h2 {
        color: white;
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .login-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0;
        font-size: 0.9rem;
    }

    .login-body {
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

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        cursor: pointer;
        accent-color: var(--maroon);
    }

    .form-check-label {
        color: var(--gray-600);
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-login {
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

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.4);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }

    .btn-link {
        color: var(--maroon);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.2s ease;
        display: inline-block;
    }

    .btn-link:hover {
        color: var(--gold);
        text-decoration: underline;
    }

    .register-prompt {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .register-prompt p {
        color: var(--gray-600);
        margin: 0;
        font-size: 0.9rem;
    }

    .register-prompt a {
        color: var(--maroon);
        font-weight: 700;
        text-decoration: none;
    }

    .register-prompt a:hover {
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
        .login-card {
            margin: 0 1rem;
        }
        
        .login-header h2 {
            font-size: 1.5rem;
        }
        
        .login-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="login-header">
                        <h2>
                            <i class="fas fa-graduation-cap" style="margin-right: 10px;"></i>
                            Welcome Back!
                        </h2>
                        <p>Sign in to continue your scholarship journey</p>
                    </div>

                    <div class="login-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope" style="margin-right: 8px; color: var(--maroon);"></i>
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
                                    autofocus
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
                                    <i class="fas fa-lock" style="margin-right: 8px; color: var(--maroon);"></i>
                                    Password
                                </label>
                                <input 
                                    id="password" 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                >
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="checkbox-wrapper">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-login">
                                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                                Login to Your Account
                            </button>

                            @if (Route::has('password.request'))
                                <div style="text-align: center;">
                                    <a class="btn-link" href="{{ route('password.request') }}">
                                        <i class="fas fa-key" style="margin-right: 5px;"></i>
                                        Forgot Your Password?
                                    </a>
                                </div>
                            @endif

                            <div class="separator">
                                <span>OR</span>
                            </div>

                            <div class="register-prompt">
                                <p>Don't have an account?</p>
                                <a href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i> Create Account Now
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