@extends('layouts.admin')

@section('title', 'Add New Scholarship')

@section('content')
<div class="container-fluid px-0">

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add New Scholarship
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.scholarships.store') }}">
                        @csrf

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            {{-- LEFT SIDE --}}
                            <div class="col-md-8">
                                {{-- BASIC INFORMATION --}}
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Basic Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        {{-- TITLE --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Scholarship Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g., JPA Scholarship Programme 2024">
                                            <small class="text-muted">Full name of the scholarship program</small>
                                        </div>

                                        {{-- PROVIDER --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Provider / Organization <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="provider" class="form-control" required value="{{ old('provider') }}" placeholder="e.g., Jabatan Perkhidmatan Awam (JPA)">
                                            <small class="text-muted">Organization offering this scholarship</small>
                                        </div>

                                        {{-- DESCRIPTION --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Description <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="description" rows="5" class="form-control" required placeholder="Describe the scholarship program, benefits, and requirements...">{{ old('description') }}</textarea>
                                            <small class="text-muted">Detailed description of the scholarship</small>
                                        </div>

                                        {{-- RAW ELIGIBILITY --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Raw Eligibility 
                                                <span class="text-muted">(Optional)</span>
                                            </label>
                                            <textarea name="raw_eligibility" rows="4" class="form-control" placeholder="Eligibility description from website or manual input">{{ old('raw_eligibility') }}</textarea>
                                            <small class="text-muted">Original eligibility text from source website</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- ELIGIBILITY CRITERIA --}}
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-clipboard-list me-2"></i>
                                            Eligibility Criteria
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        {{-- SPM --}}
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Minimum SPM A's</label>
                                                <input type="number" name="min_spm_as" class="form-control" min="0" max="12" value="{{ old('min_spm_as') }}" placeholder="e.g., 5">
                                                <small class="text-muted">Minimum number of A's required</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Maximum SPM A's</label>
                                                <input type="number" name="max_spm_as" class="form-control" min="0" max="12" value="{{ old('max_spm_as') }}" placeholder="e.g., 9">
                                                <small class="text-muted">Maximum number of A's expected</small>
                                            </div>
                                        </div>

                                        {{-- MONTHLY INCOME --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Maximum Monthly Household Income (RM)</label>
                                            <input type="number" step="0.01" name="max_monthly_income" class="form-control" value="{{ old('max_monthly_income') }}" placeholder="e.g., 5000">
                                            <small class="text-muted">Income threshold for eligibility (if applicable)</small>
                                        </div>

                                        
                                        <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            Income Categories
                                        </label>

                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="income_categories[]"
                                                        value="B40"
                                                        id="income_b40">

                                                    <label class="form-check-label"
                                                        for="income_b40">
                                                        B40
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="income_categories[]"
                                                        value="M40"
                                                        id="income_m40">

                                                    <label class="form-check-label"
                                                        for="income_m40">
                                                        M40
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="income_categories[]"
                                                        value="T20"
                                                        id="income_t20">

                                                    <label class="form-check-label"
                                                        for="income_t20">
                                                        T20
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                        {{-- STUDY LEVEL --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Study Levels</label>
                                            @php $selectedStudyPaths = old('study_paths', []); @endphp
                                            <div class="row">
                                                @foreach (['Foundation', 'Matriculation', 'Diploma', 'Degree', 'TVET', 'Postgraduate'] as $level)
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="study_paths[]" value="{{ $level }}" id="study_{{ $level }}" {{ in_array($level, $selectedStudyPaths) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="study_{{ $level }}">{{ $level }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted">Select all applicable study levels</small>
                                        </div>

                                        {{-- FIELD OF STUDY --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Fields of Study</label>
                                            @php
                                                $fieldOptions = ['Computer Science', 'Engineering', 'Business', 'Medicine', 'Education', 'TVET', 'Data Science', 'Finance', 'Accounting', 'Economics', 'Law', 'Actuarial Science', 'Mathematics', 'Statistics', 'Science', 'Physics', 'Chemistry', 'Biological Science', 'Pharmacy', 'Environmental Science', 'Architecture', 'Technical', 'Social Science', 'Communication', 'Hospitality', 'Anthropology', 'History', 'Linguistics', 'Performing Arts', 'Philosophy', 'Art & Design', 'Archaeology'];
                                                $selectedFields = old('fields_of_study', []);
                                            @endphp
                                            <div class="row">
                                                @foreach ($fieldOptions as $field)
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="fields_of_study[]" value="{{ $field }}" id="field_{{ Str::slug($field) }}" {{ in_array($field, $selectedFields) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="field_{{ Str::slug($field) }}">{{ $field }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted">Select fields of study covered by this scholarship</small>
                                        </div>


                                        {{-- CITIZENSHIP + STATE --}}
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Citizenship Required</label>
                                                <input type="text" name="citizenship_required" class="form-control" placeholder="Example: Malaysia" value="{{ old('citizenship_required') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">State Requirement</label>
                                                <input type="text" name="state_requirement" class="form-control" placeholder="Optional" value="{{ old('state_requirement') }}">
                                            </div>
                                        </div>

                                        {{-- AGE --}}
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Minimum Age</label>
                                                <input type="number" name="min_age" class="form-control" value="{{ old('min_age') }}" placeholder="e.g., 17">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Maximum Age</label>
                                                <input type="number" name="max_age" class="form-control" value="{{ old('max_age') }}" placeholder="e.g., 25">
                                            </div>
                                        </div>

                                        {{-- PRIORITY OPTIONS --}}
                                        <div class="mt-3">
                                            <h6 class="mb-3 fw-semibold">Priority Options</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="bumiputera_required" value="1" id="bumiputera_required" {{ old('bumiputera_required') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="bumiputera_required">Bumiputera Required</label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="bumiputera_priority" value="1" id="bumiputera_priority" {{ old('bumiputera_priority') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="bumiputera_priority">Bumiputera Priority</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="leadership_required" value="1" id="leadership_required" {{ old('leadership_required') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="leadership_required">Leadership Required</label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="leadership_priority" value="1" id="leadership_priority" {{ old('leadership_priority') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="leadership_priority">Leadership Priority</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PRIORITY WEIGHT --}}
                                        <div class="mt-4">
                                            <label class="form-label fw-semibold">Priority Weight (1-10)</label>
                                            <input type="number" name="priority_weight" class="form-control" min="1" max="10" value="{{ old('priority_weight', 1) }}">
                                            <small class="text-muted">Higher weight = higher recommendation priority</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT SIDE --}}
                            <div class="col-md-4">
                                {{-- APPLICATION DETAILS --}}
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Application Details
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        {{-- DEADLINE --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Application Deadline</label>
                                            <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                                            <small class="text-muted">Leave empty for rolling deadline</small>
                                        </div>

                                        {{-- LINK --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Application Link</label>
                                            <input type="url" name="application_link" class="form-control" value="{{ old('application_link') }}" placeholder="https://example.com/apply">
                                            <small class="text-muted">Official application URL</small>
                                        </div>

                                        {{-- OFFICIAL --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Official Scholarship</label>
                                            <select name="is_official" class="form-select">
                                                <option value="1" {{ old('is_official') == '1' ? 'selected' : '' }}>Yes - Official Scholarship</option>
                                                <option value="0" {{ old('is_official') == '0' ? 'selected' : '' }}>No - Third Party</option>
                                            </select>
                                        </div>

                                        {{-- ACTIVE --}}
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Active Status</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active - Visible to Students</option>
                                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive - Hidden</option>
                                            </select>
                                            <small class="text-muted">Inactive scholarships won't appear in search results</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- HELPFUL TIPS --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-lightbulb me-2"></i>
                                            Helpful Tips
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex mb-3">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <small>Include complete eligibility criteria for better matching</small>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <small>Add application deadline to show urgency status</small>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <small>Provide accurate application link for students</small>
                                        </div>
                                        <div class="d-flex">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <small>Set priority weight based on scholarship prestige</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.scholarships.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Create Scholarship
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-bottom: 2px solid #F4C542;
        padding: 15px 20px;
        font-weight: 700;
        color: #7A0019;
    }
    
    .card-header h5, .card-header h6 {
        color: #7A0019;
        font-weight: 700;
    }
    
    .form-label {
        color: #374151;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
        outline: none;
    }
    
    .form-check-input:checked {
        background-color: #7A0019;
        border-color: #7A0019;
    }
    
    .form-check-input:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 0.2rem rgba(244, 197, 66, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 60px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, #4e0010, #7A0019);
    }
    
    .btn-secondary {
        background: #6b7280;
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 60px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: none;
        border-left: 4px solid #dc2626;
        border-radius: 16px;
        color: #991b1b;
    }
    
    .text-danger {
        color: #dc2626 !important;
    }
    
    .text-muted {
        color: #6b7280 !important;
        font-size: 0.8rem;
    }
    
    .fw-semibold {
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .card-header {
            padding: 12px 15px;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .form-control, .form-select {
            padding: 8px 12px;
        }
        
        .btn-primary, .btn-secondary {
            padding: 0.5rem 1rem;
        }
    }
</style>
@endpush

@endsection