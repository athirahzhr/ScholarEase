@extends('layouts.app')

@section('title', 'Share Your Feedback')

@section('content')
<style>
    :root {
        --maroon: #7A0019;
        --maroon-dark: #4e0010;
        --gold: #F4C542;
        --cream: #FFF8EE;
    }

    .feedback-container {
        background: linear-gradient(135deg, var(--cream) 0%, #f5ebe0 100%);
        min-height: calc(100vh - 200px);
        padding: 3rem 0;
    }

    .feedback-card {
        background: white;
        border-radius: 28px;
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .feedback-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 2rem;
        text-align: center;
        border-bottom: 3px solid var(--gold);
    }

    .feedback-header h2 {
        color: white;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .feedback-header p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0;
    }

    .feedback-body {
        padding: 2rem;
    }

    .rating-container {
        text-align: center;
        padding: 1rem;
    }

    .rating-stars {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin: 20px 0;
    }

    .rating-stars input {
        display: none;
    }

    .rating-stars label {
        font-size: 2.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #d1d5db;
    }

    .rating-stars label:hover,
    .rating-stars label:hover ~ label,
    .rating-stars input:checked ~ label {
        color: #F4C542;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
        outline: none;
    }

    .btn-submit {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.875rem 2rem;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-left: 4px solid #10b981;
        border-radius: 12px;
        color: #065f46;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .feedback-header {
            padding: 1.5rem;
        }
        
        .feedback-body {
            padding: 1.5rem;
        }
        
        .rating-stars label {
            font-size: 1.8rem;
        }
    }
</style>

<div class="feedback-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="feedback-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="feedback-header">
                        <h2>
                            <i class="fas fa-star me-2"></i>
                            Share Your Experience
                        </h2>
                        <p>Help us improve ScholarEase by sharing your feedback</p>
                    </div>

                    <div class="feedback-body">
                        @if(session('success'))
                            <div class="alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('feedback.store') }}" method="POST">
                            @csrf

                            <div class="rating-container">
                                <label class="form-label text-center d-block">How would you rate ScholarEase?</label>
                                <div class="rating-stars">
                                    <input type="radio" name="rating" value="5" id="star5" required>
                                    <label for="star5" class="fas fa-star"></label>
                                    
                                    <input type="radio" name="rating" value="4" id="star4">
                                    <label for="star4" class="fas fa-star"></label>
                                    
                                    <input type="radio" name="rating" value="3" id="star3">
                                    <label for="star3" class="fas fa-star"></label>
                                    
                                    <input type="radio" name="rating" value="2" id="star2">
                                    <label for="star2" class="fas fa-star"></label>
                                    
                                    <input type="radio" name="rating" value="1" id="star1">
                                    <label for="star1" class="fas fa-star"></label>
                                </div>
                                @error('rating')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-comment me-2" style="color: var(--maroon);"></i>
                                    Your Feedback
                                </label>
                                <textarea 
                                    name="comment" 
                                    rows="5" 
                                    class="form-control" 
                                    placeholder="Tell us about your experience with ScholarEase. What did you like? What can we improve?"
                                    required
                                >{{ old('comment') }}</textarea>
                                @error('comment')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="fas fa-paper-plane me-2"></i>
                                Submit Feedback
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>
                                Your feedback helps us serve you better. Thank you!
                            </small>
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