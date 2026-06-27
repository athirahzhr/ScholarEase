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
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .profile-container {
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    .profile-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        border: 1px solid rgba(122, 0, 25, 0.08);
    }

    .profile-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 1.75rem 2.5rem;
        border-bottom: 4px solid var(--gold);
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(244, 197, 66, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }

    .profile-header h3 {
        color: white;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .profile-header h3 i {
        color: var(--gold);
    }

    .profile-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0;
        font-size: 0.95rem;
        position: relative;
        z-index: 1;
    }

    .profile-body {
        padding: 2.5rem;
    }

    .form-section {
        background: #faf8f6;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #f0ece6;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        border-color: var(--gold);
        box-shadow: var(--shadow-sm);
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--maroon);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gold);
        display: inline-block;
        letter-spacing: 0.5px;
    }

    .form-section-title i {
        color: var(--gold);
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    .form-label i {
        color: var(--maroon);
        margin-right: 8px;
        width: 18px;
        text-align: center;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.7rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: white;
        color: var(--gray-800);
    }

    .form-control:hover, .form-select:hover {
        border-color: #c5c7ca;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 4px rgba(244, 197, 66, 0.15);
        background: white;
    }

    .form-control::placeholder {
        color: #aab0b9;
        font-size: 0.85rem;
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%237A0019' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    .form-select option {
        padding: 0.5rem;
    }

    .form-check {
        margin-right: 2rem;
        padding-left: 2rem;
    }

    .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0.1rem;
        cursor: pointer;
        border: 2px solid #d1d5db;
        transition: all 0.2s ease;
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
        font-weight: 500;
    }

    .form-check-label i {
        color: var(--maroon);
        margin-right: 4px;
    }

    .btn-save {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        padding: 0.85rem 2.5rem;
        border: none;
        border-radius: 40px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 16px rgba(122, 0, 25, 0.3);
        letter-spacing: 0.5px;
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(122, 0, 25, 0.4);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }

    .btn-save:active {
        transform: translateY(0px);
    }

    .required-field::after {
        content: '*';
        color: #dc2626;
        margin-left: 4px;
        font-weight: 700;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: none;
        border-left: 4px solid #dc2626;
        border-radius: 12px;
        color: #991b1b;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .alert-danger ul {
        padding-left: 1.5rem;
        margin-top: 0.5rem;
    }

    .text-muted-sm {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
        display: block;
    }

    .form-hint {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
        display: block;
    }

    .checkbox-group {
        background: #f8f6f4;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        border: 1px solid #e8e4de;
    }

    .checkbox-group .form-check {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 1.2rem 1.5rem;
        }
        
        .profile-body {
            padding: 1.25rem;
        }

        .form-section {
            padding: 1rem;
        }

        .btn-save {
            width: 100%;
            justify-content: center;
            padding: 0.85rem 1.5rem;
        }

        .form-check {
            margin-right: 1rem;
            margin-bottom: 0.5rem;
        }

        .checkbox-group {
            padding: 0.75rem 1rem;
        }

        .checkbox-group .form-check {
            margin-bottom: 0.25rem;
        }
    }

    @media (max-width: 576px) {
        .profile-header h3 {
            font-size: 1.2rem;
        }

        .form-section-title {
            font-size: 0.9rem;
        }
    }

    /* Custom styling for number inputs */
    input[type="number"] {
        -moz-appearance: textfield;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Progress indicator */
    .step-indicator {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .step-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #d1d5db;
        transition: all 0.3s ease;
    }

    .step-dot.active {
        background: var(--gold);
        width: 24px;
        border-radius: 4px;
    }

    .step-dot.completed {
        background: var(--maroon);
    }

    .field-icon {
        color: var(--maroon);
        opacity: 0.6;
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
                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Academic Information
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label required-field">
                                            <i class="fas fa-star"></i>
                                            Total A's (SPM)
                                        </label>
                                        <input
                                            type="number"
                                            id="total_as"
                                            name="total_as"
                                            class="form-control"
                                            value="{{ old('total_as', $profile->total_as ?? 0) }}"
                                            readonly
                                        >
                                        <span class="form-hint">Number of A's in SPM (0-12)</span>
                                    </div>

                                    @if(!empty($profile?->spm_results))

                                    <div class="col-12 mt-3">

                                        <label class="form-label">
                                            <i class="fas fa-file-alt"></i>
                                            OCR Extracted SPM Results
                                        </label>

                                        <div class="table-responsive">

                                            <table class="table table-bordered">

                                                <thead>
                                                    <tr>
                                                        <th>Subject</th>
                                                        <th>Grade</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                @foreach($profile->spm_results as $subject => $grade)

                                                    <tr>
                                                        <td>{{ $subject }}</td>
                                                        <td>

                                                    <select
                                                        name="spm_results[{{ $subject }}]"
                                                        class="form-select spm-grade"
                                                    >

                                                    @foreach([
                                                    'A+',
                                                    'A',
                                                    'A-',
                                                    'B+',
                                                    'B',
                                                    'C+',
                                                    'C',
                                                    'D',
                                                    'E',
                                                    'G'
                                                    ] as $g)

                                                    <option
                                                    value="{{ $g }}"
                                                    {{ $grade == $g ? 'selected' : '' }}
                                                    >

                                                    {{ $g }}

                                                    </option>

                                                    @endforeach

                                                    </select>

                                                    </td>
                                                    </tr>

                                                @endforeach

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                    @endif

                                    <div class="col-md-4">
                                        <label class="form-label required-field">
                                            <i class="fas fa-money-bill-wave"></i>
                                            Monthly Household Income (RM)
                                        </label>
                                        <input
                                            type="number"
                                            name="monthly_income"
                                            class="form-control"
                                            min="0"
                                            step="0.01"
                                            required
                                            value="{{ old('monthly_income', $profile->monthly_income ?? '') }}"
                                            placeholder="e.g., 4500"
                                        >
                                        <span class="form-hint">Enter your family's total monthly income</span>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label required-field">
                                            <i class="fas fa-road"></i>
                                            Study Path
                                        </label>
                                        <select name="study_path" class="form-select" required>
                                            <option value="">Select Study Path</option>
                                            @php $studyPaths = ['Foundation', 'Matriculation', 'Diploma', 'Degree', 'TVET']; @endphp
                                            @foreach ($studyPaths as $level)
                                                <option value="{{ $level }}" {{ old('study_path', $profile->study_level ?? '') == $level ? 'selected' : '' }}>
                                                    {{ $level }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mt-3">
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
                            </div>

                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="fas fa-user-circle me-2"></i>
                                    Personal Information
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label required-field">
                                            <i class="fas fa-map-marker-alt"></i>
                                            State
                                        </label>
                                        <select name="state" class="form-select" required>
                                            <option value="">Select Your State</option>
                                            @php
                                            $states = [
                                                'Johor', 'Kedah', 'Kelantan', 'Kuala Lumpur', 'Labuan', 
                                                'Melaka', 'Negeri Sembilan', 'Pahang', 'Penang', 'Perak', 
                                                'Perlis', 'Putrajaya', 'Sabah', 'Sarawak', 'Selangor', 
                                                'Terengganu'
                                            ];
                                            @endphp
                                            @foreach ($states as $state)
                                                <option value="{{ $state }}" {{ old('state', $profile->state ?? '') == $state ? 'selected' : '' }}>
                                                    {{ $state }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label required-field">
                                            <i class="fas fa-passport"></i>
                                            Citizenship
                                        </label>
                                        <select name="citizenship" class="form-select" required>
                                            <option value="">Select Citizenship</option>
                                            <option value="Malaysia" {{ old('citizenship', $profile->citizenship ?? '') == 'Malaysia' ? 'selected' : '' }}>
                                                <i class="fas fa-flag"></i> Malaysia
                                            </option>
                                            <option value="International" {{ old('citizenship', $profile->citizenship ?? '') == 'International' ? 'selected' : '' }}>
                                                <i class="fas fa-globe"></i> International
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label required-field">
                                            <i class="fas fa-birthday-cake"></i>
                                            Age
                                        </label>
                                        <select name="age" class="form-select" required>
                                            <option value="">Select Your Age</option>
                                            @php
                                            $currentYear = date('Y');
                                            $birthYears = range(2000, $currentYear - 15);
                                            @endphp
                                            @foreach ($birthYears as $year)
                                                @php $age = $currentYear - $year; @endphp
                                                <option value="{{ $age }}" {{ old('age', $profile->age ?? '') == $age ? 'selected' : '' }}>
                                                    {{ $age }} years old (Born {{ $year }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="form-hint">Minimum age for scholarship applications</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information Section -->
                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Additional Information
                                </h5>

                                <div class="checkbox-group">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="hidden" name="bumiputera" value="0">
                                                <input class="form-check-input" type="checkbox" name="bumiputera" value="1" id="bumiputera" {{ old('bumiputera', $profile->bumiputera ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="bumiputera">
                                                    <i class="fas fa-star-of-life"></i> Bumiputera
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="hidden" name="has_leadership" value="0">
                                                <input class="form-check-input" type="checkbox" name="has_leadership" value="1" id="leadership" {{ old('has_leadership', $profile->has_leadership ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="leadership">
                                                    <i class="fas fa-trophy"></i> Leadership Experience
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="form-hint mt-2">Select all that apply to you</span>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-4 pt-2 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <span class="text-muted" style="font-size: 0.85rem;">
                                        <i class="fas fa-info-circle"></i> All fields marked with <span style="color: #dc2626;">*</span> are required
                                    </span>
                                </div>
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save"></i>
                                    Save & Refresh Recommendations
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

AOS.init({
    duration: 800,
    once: true
});

// =========================
// AUTO UPDATE TOTAL A
// =========================

function updateTotalAs() {

    let total = 0;

    document.querySelectorAll('.spm-grade').forEach(function(select) {

        if (['A+', 'A', 'A-'].includes(select.value)) {

            total++;

        }

    });

    const input = document.getElementById('total_as');

    if (input) {

        input.value = total;

    }

}

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.spm-grade').forEach(function(select) {

        select.addEventListener('change', updateTotalAs);

    });

    updateTotalAs();

});

</script>

@endpush
@endsection