@extends('layouts.app')

@section('title', 'My Profile')

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

    .profile-container {
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    .profile-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 1.5rem 2rem;
        border-bottom: 3px solid var(--gold);
    }

    .profile-header h3 {
        color: white;
        font-weight: 700;
        margin: 0;
    }

    .profile-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0;
        font-size: 0.9rem;
    }

    .profile-body {
        padding: 2rem;
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--maroon);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gold);
        display: inline-block;
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.85rem;
    }

    .form-label i {
        color: var(--maroon);
        margin-right: 8px;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.7rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
    }

    .form-check {
        margin-right: 1.5rem;
    }

    .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        margin-top: 0.1rem;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: var(--maroon);
        border-color: var(--maroon);
    }

    .form-check-input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(244, 197, 66, 0.25);
    }

    .form-check-label {
        color: var(--gray-600);
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-save {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 40px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.4);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }

    .required-field::after {
        content: '*';
        color: #dc2626;
        margin-left: 4px;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: none;
        border-left: 4px solid #dc2626;
        border-radius: 12px;
        color: #991b1b;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 1.2rem 1.5rem;
        }
        
        .profile-body {
            padding: 1.5rem;
        }
        
        .btn-save {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="profile-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="profile-header">
                        <h3>
                            <i class="fas fa-user-edit me-2"></i>
                            Complete Your Profile
                        </h3>
                        <p>Help us find the best scholarships for you by providing your academic and personal information</p>
                    </div>

                    <div class="profile-body">
                        @if($errors->any())
                            <div class="alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.store') }}">
                            @csrf

                            <!-- Academic Information Section -->
                            <div class="mb-4">
                                <h5 class="form-section-title">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Academic Information
                                </h5>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label required-field">
                                        <i class="fas fa-star"></i>
                                        Total A's (SPM)
                                    </label>
                                    <input
                                        type="number"
                                        name="total_as"
                                        class="form-control"
                                        min="0"
                                        max="12"
                                        required
                                        value="{{ old('total_as', $profile->total_as ?? '') }}"
                                        placeholder="e.g., 8"
                                    >
                                    <small class="text-muted">Number of A's in SPM (0-12)</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label required-field">
                                        <i class="fas fa-chart-line"></i>
                                        Income Category
                                    </label>
                                    <select name="income_category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <option value="B40" {{ old('income_category', $profile->income_category ?? '') == 'B40' ? 'selected' : '' }}>B40 - Lower Income (Below RM4,850)</option>
                                        <option value="M40" {{ old('income_category', $profile->income_category ?? '') == 'M40' ? 'selected' : '' }}>M40 - Middle Income (RM4,850 - RM10,959)</option>
                                        <option value="T20" {{ old('income_category', $profile->income_category ?? '') == 'T20' ? 'selected' : '' }}>T20 - Upper Income (Above RM10,959)</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label required-field">
                                        <i class="fas fa-road"></i>
                                        Study Path
                                    </label>
                                    <select name="study_path" class="form-select" required>
                                        <option value="">Select Study Path</option>
                                        @php $studyPaths = ['Foundation', 'Matriculation', 'Diploma', 'Degree', 'TVET', 'Postgraduate']; @endphp
                                        @foreach ($studyPaths as $level)
                                            <option value="{{ $level }}" {{ old('study_path', $profile->study_level ?? '') == $level ? 'selected' : '' }}>
                                                {{ $level }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label required-field">
                                        <i class="fas fa-book"></i>
                                        Field of Study
                                    </label>
                                    <select name="field_of_study" class="form-select" required>
                                        <option value="">Select Field of Study</option>
                                        @php
                                        $fieldOptions = ['Computer Science', 'Engineering', 'Business', 'Medicine', 'Education', 'TVET', 'Data Science', 'Finance', 'Accounting', 'Economics', 'Law', 'Actuarial Science', 'Mathematics', 'Statistics', 'Science', 'Physics', 'Chemistry', 'Biological Science', 'Pharmacy', 'Environmental Science', 'Architecture', 'Technical', 'Social Science', 'Communication', 'Hospitality', 'Anthropology', 'History', 'Linguistics', 'Performing Arts', 'Philosophy', 'Art & Design', 'Archaeology'];
                                        @endphp
                                        @foreach ($fieldOptions as $field)
                                            <option value="{{ $field }}" {{ old('field_of_study', $profile->field_of_study ?? '') == $field ? 'selected' : '' }}>
                                                {{ $field }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Personal Information Section -->
                            <div class="mb-4 mt-4">
                                <h5 class="form-section-title">
                                    <i class="fas fa-user-circle me-2"></i>
                                    Personal Information
                                </h5>
                            </div>

                                <div class="col-md-3">
                                    <label class="form-label required-field">
                                        <i class="fas fa-map-marker-alt"></i>
                                        State
                                    </label>
                                    <input type="text" name="state" class="form-control" required value="{{ old('state', $profile->state ?? '') }}" placeholder="e.g., Selangor, Kuala Lumpur">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label required-field">
                                        <i class="fas fa-passport"></i>
                                        Citizenship
                                    </label>
                                    <select name="citizenship" class="form-select" required>
                                        <option value="">-- Select --</option>
                                        <option value="Malaysia" {{ old('citizenship', $profile->citizenship ?? '') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                        <option value="International" {{ old('citizenship', $profile->citizenship ?? '') == 'International' ? 'selected' : '' }}>International</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Information Section -->
                            <div class="mb-4 mt-4">
                                <h5 class="form-section-title">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Additional Information
                                </h5>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="bumiputera" value="1" id="bumiputera" {{ old('bumiputera', $profile->bumiputera ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bumiputera">
                                            <i class="fas fa-star-of-life me-1"></i> Bumiputera
                                        </label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="has_leadership" value="1" id="leadership" {{ old('has_leadership', $profile->has_leadership ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="leadership">
                                            <i class="fas fa-trophy me-1"></i> Leadership Experience
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-4 pt-3">
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save"></i>
                                    Save Profile
                                </button>
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
    
    // Preselect values from old input or profile data
    document.addEventListener('DOMContentLoaded', function() {
        // Set income category
        const incomeCategory = "{{ old('income_category', $profile->income_category ?? '') }}";
        if(incomeCategory) {
            const incomeSelect = document.querySelector('select[name="income_category"]');
            if(incomeSelect) incomeSelect.value = incomeCategory;
        }
        
        // Set citizenship
        const citizenship = "{{ old('citizenship', $profile->citizenship ?? '') }}";
        if(citizenship) {
            const citizenshipSelect = document.querySelector('select[name="citizenship"]');
            if(citizenshipSelect) citizenshipSelect.value = citizenship;
        }
    });
</script>
@endpush
@endsection