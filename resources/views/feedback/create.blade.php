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
        padding: 1rem 1rem 1.5rem;
    }

    .rating-stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 10px;
        margin: 15px 0;
    }

    .rating-stars input {
        display: none;
    }

    .rating-stars label {
        font-size: 2.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #d1d5db;
        user-select: none;
    }

    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #F4C542;
        transform: scale(1.1);
    }

    .rating-stars input:checked ~ label {
        color: #F4C542;
    }

    .rating-stars label i {
        transition: all 0.2s ease;
    }

    .rating-stars label:hover i {
        transform: scale(1.1);
    }

    .rating-text {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-top: 10px;
        min-height: 28px;
    }

    .rating-text .text-muted {
        color: #9ca3af !important;
        font-weight: 400;
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
        font-size: 1rem;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
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
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-text {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .text-danger {
        color: #dc2626 !important;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        display: block;
    }

    @media (max-width: 768px) {
        .feedback-header {
            padding: 1.5rem;
        }
        
        .feedback-body {
            padding: 1.5rem;
        }
        
        .rating-stars label {
            font-size: 2.2rem;
        }
    }

    @media (max-width: 576px) {
        .rating-stars label {
            font-size: 1.8rem;
        }
        
        .feedback-body {
            padding: 1rem;
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
                                <i class="fas fa-check-circle fa-lg"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('feedback.store') }}" method="POST" id="feedbackForm">
                            @csrf

                            <div class="rating-container">
                                <label class="form-label text-center d-block" style="font-size: 1.05rem;">
                                    <i class="fas fa-star me-2" style="color: var(--gold);"></i>
                                    How would you rate ScholarEase?
                                </label>
                                
                                <div class="rating-stars">
                                    <input type="radio" name="rating" value="5" id="star5">
                                    <label for="star5" class="fas fa-star" data-rating="5"></label>
                                    
                                    <input type="radio" name="rating" value="4" id="star4">
                                    <label for="star4" class="fas fa-star" data-rating="4"></label>
                                    
                                    <input type="radio" name="rating" value="3" id="star3">
                                    <label for="star3" class="fas fa-star" data-rating="3"></label>
                                    
                                    <input type="radio" name="rating" value="2" id="star2">
                                    <label for="star2" class="fas fa-star" data-rating="2"></label>
                                    
                                    <input type="radio" name="rating" value="1" id="star1">
                                    <label for="star1" class="fas fa-star" data-rating="1"></label>
                                </div>

                                <div class="rating-text" id="ratingText">
                                    <span class="text-muted">Select a rating</span>
                                </div>
                                
                                @error('rating')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="comment">
                                    <i class="fas fa-comment me-2" style="color: var(--maroon);"></i>
                                    Your Feedback
                                </label>
                                <textarea 
                                    name="comment" 
                                    id="comment"
                                    rows="5" 
                                    class="form-control @error('comment') is-invalid @enderror" 
                                    placeholder="Tell us about your experience with ScholarEase. What did you like? What can we improve?"
                                    required
                                >{{ old('comment') }}</textarea>
                                @error('comment')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Your feedback is valuable and will help us improve.
                                </div>
                            </div>

                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="fas fa-paper-plane"></i>
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
    // ============================================
    // INITIALIZE AOS
    // ============================================
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, once: true });
    }

    // ============================================
    // STAR RATING FUNCTIONALITY
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-stars label');
        const ratingText = document.getElementById('ratingText');
        const ratingInputs = document.querySelectorAll('.rating-stars input');
        
        // Rating descriptions
        const ratingDescriptions = {
            1: '⭐ Terrible - Needs major improvement',
            2: '⭐ Poor - Below expectations',
            3: '⭐ Okay - Average experience',
            4: '⭐ Good - Satisfied with the experience',
            5: '⭐⭐⭐⭐⭐ Excellent - Amazing experience!'
        };

        // Function to update rating text
        function updateRatingText(value) {
            if (value && ratingDescriptions[value]) {
                ratingText.innerHTML = `<span style="color: #7A0019; font-weight: 700;">${ratingDescriptions[value]}</span>`;
            } else {
                ratingText.innerHTML = `<span class="text-muted">Select a rating</span>`;
            }
        }

        // Add click event to each star label
        stars.forEach(label => {
            label.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                const radio = document.getElementById('star' + rating);
                if (radio) {
                    radio.checked = true;
                    updateRatingText(rating);
                }
            });

            // Add hover effect
            label.addEventListener('mouseenter', function() {
                const rating = this.getAttribute('data-rating');
                const allLabels = document.querySelectorAll('.rating-stars label');
                
                allLabels.forEach(lbl => {
                    const lblRating = parseInt(lbl.getAttribute('data-rating'));
                    if (lblRating <= parseInt(rating)) {
                        lbl.style.color = '#F4C542';
                    } else {
                        lbl.style.color = '#d1d5db';
                    }
                });
            });

            label.addEventListener('mouseleave', function() {
                const allLabels = document.querySelectorAll('.rating-stars label');
                const checkedRadio = document.querySelector('.rating-stars input:checked');
                const selectedRating = checkedRadio ? checkedRadio.value : null;
                
                allLabels.forEach(lbl => {
                    const lblRating = lbl.getAttribute('data-rating');
                    if (selectedRating && parseInt(lblRating) <= parseInt(selectedRating)) {
                        lbl.style.color = '#F4C542';
                    } else {
                        lbl.style.color = '#d1d5db';
                    }
                });
            });
        });

        // Check if any rating is already selected (for old() values)
        const preChecked = document.querySelector('.rating-stars input:checked');
        if (preChecked) {
            updateRatingText(preChecked.value);
        }

        // Form validation
        const form = document.getElementById('feedbackForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form) {
            form.addEventListener('submit', function(e) {
                const checkedRadio = document.querySelector('.rating-stars input:checked');
                if (!checkedRadio) {
                    e.preventDefault();
                    const errorDiv = document.createElement('span');
                    errorDiv.className = 'text-danger';
                    errorDiv.textContent = 'Please select a rating before submitting.';
                    
                    const container = document.querySelector('.rating-container');
                    const existingError = container.querySelector('.text-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    container.appendChild(errorDiv);
                    
                    // Scroll to rating section
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    });
</script>
@endpush
@endsection