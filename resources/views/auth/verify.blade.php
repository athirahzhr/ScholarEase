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

    .verify-wrapper {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        padding: 4rem 1rem;
        position: relative;
        overflow: hidden;
    }

    .verify-wrapper::before {
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

    .verify-wrapper::after {
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

    .verify-card {
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

    .verify-card:hover {
        transform: translateY(-5px);
    }

    .verify-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 2rem;
        text-align: center;
        border-bottom: 3px solid var(--gold);
    }

    .verify-header h2 {
        color: white;
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .verify-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0;
        font-size: 0.9rem;
    }

    .verify-body {
        padding: 2rem;
        text-align: center;
    }

    .verify-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(244,197,66,0.1));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .verify-icon i {
        font-size: 2.5rem;
        color: var(--maroon);
    }

    .verify-message {
        color: var(--gray-800);
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .verify-instruction {
        color: var(--gray-600);
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }

    .resend-form {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .resend-text {
        color: var(--gray-600);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .btn-resend {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
    }

    .btn-resend:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.4);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-left: 4px solid #10b981;
        border-radius: 16px;
        color: #065f46;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success i {
        font-size: 1.2rem;
    }

    .back-to-login {
        margin-top: 1.5rem;
    }

    .back-to-login a {
        color: var(--maroon);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .back-to-login a:hover {
        color: var(--gold);
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .verify-card {
            margin: 0 1rem;
        }
        
        .verify-header h2 {
            font-size: 1.5rem;
        }
        
        .verify-body {
            padding: 1.5rem;
        }
        
        .verify-icon {
            width: 60px;
            height: 60px;
        }
        
        .verify-icon i {
            font-size: 2rem;
        }
        
        .btn-resend {
            padding: 0.6rem 1.2rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="verify-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="verify-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="verify-header">
                        <h2>
                            <i class="fas fa-envelope" style="margin-right: 10px;"></i>
                            Verify Email
                        </h2>
                        <p>Confirm your email address to continue</p>
                    </div>

                    <div class="verify-body">
                        <div class="verify-icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>

                        @if (session('resent'))
                            <div class="alert-success" role="alert">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ __('A fresh verification link has been sent to your email address.') }}</span>
                            </div>
                        @endif

                        <div class="verify-message">
                            <strong>{{ __('Before proceeding, please check your email for a verification link.') }}</strong>
                        </div>

                        <div class="verify-instruction">
                            <i class="fas fa-info-circle" style="color: var(--gold); margin-right: 8px;"></i>
                            {{ __('If you didn\'t receive the email, check your spam folder or click the button below to request a new one.') }}
                        </div>

                        <div class="resend-form">
                            <div class="resend-text">
                                {{ __("Didn't receive the verification email?") }}
                            </div>
                            <form method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn-resend">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ __('Resend Verification Link') }}
                                </button>
                            </form>
                        </div>

                        <div class="back-to-login">
                            <a href="{{ route('login') }}">
                                <i class="fas fa-arrow-left me-1"></i>
                                {{ __('Back to Login') }}
                            </a>
                        </div>
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