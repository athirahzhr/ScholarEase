@extends('layouts.admin')

@section('title', 'Edit Scholarship')

@section('content')

@php
    $criteria = $scholarship->eligibilityCriteria;
    $deadline = $scholarship->deadline ? \Carbon\Carbon::parse($scholarship->deadline) : null;
@endphp

<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold text-maroon">
                <i class="fas fa-edit me-2"></i>
                Edit Scholarship
            </h4>
            <p class="text-muted mb-0 mt-1">Update scholarship information and eligibility criteria</p>
        </div>
        <a href="{{ route('admin.scholarships.show', $scholarship->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Details
        </a>
    </div>

    <form method="POST" action="{{ route('admin.scholarships.update', $scholarship->id) }}" id="scholarshipForm">
        @csrf
        @method('PUT')

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- ============================================ --}}
            {{-- LEFT COLUMN - Main Information               --}}
            {{-- ============================================ --}}
            <div class="col-lg-8">

                {{-- BASIC INFORMATION --}}
                <div class="card shadow-sm border-0 mb-4">
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
                            <input type="text" 
                                   name="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $scholarship->title) }}" 
                                   required 
                                   placeholder="e.g., JPA Scholarship Programme 2024">
                            <small class="text-muted">Full name of the scholarship program</small>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PROVIDER --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Provider / Organization <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="provider" 
                                   class="form-control @error('provider') is-invalid @enderror" 
                                   value="{{ old('provider', $scholarship->provider) }}" 
                                   required 
                                   placeholder="e.g., Jabatan Perkhidmatan Awam (JPA)">
                            <small class="text-muted">Organization offering this scholarship</small>
                            @error('provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" 
                                      rows="5" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      required 
                                      placeholder="Describe the scholarship program, benefits, and requirements...">{{ old('description', $scholarship->description) }}</textarea>
                            <small class="text-muted">Detailed description of the scholarship</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- RAW ELIGIBILITY --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold">
                                Raw Eligibility 
                                <span class="text-muted">(Optional)</span>
                            </label>
                            <textarea name="raw_eligibility" 
                                      rows="4" 
                                      class="form-control @error('raw_eligibility') is-invalid @enderror" 
                                      placeholder="Eligibility description from website or manual input">{{ old('raw_eligibility', $scholarship->raw_eligibility) }}</textarea>
                            <small class="text-muted">Original eligibility text from source website</small>
                            @error('raw_eligibility')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ELIGIBILITY CRITERIA --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Eligibility Criteria
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- SPM A's --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Minimum SPM A's</label>
                                <input type="number" 
                                       name="min_spm_as" 
                                       class="form-control @error('min_spm_as') is-invalid @enderror" 
                                       min="0" 
                                       max="12" 
                                       value="{{ old('min_spm_as', $criteria->min_spm_as ?? '') }}" 
                                       placeholder="e.g., 5">
                                <small class="text-muted">Minimum number of A's required</small>
                                @error('min_spm_as')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- MAX MONTHLY INCOME --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Maximum Monthly Income (RM)</label>
                                <input type="number" 
                                       step="0.01" 
                                       name="max_monthly_income" 
                                       class="form-control @error('max_monthly_income') is-invalid @enderror" 
                                       value="{{ old('max_monthly_income', $criteria->max_monthly_income ?? '') }}" 
                                       placeholder="e.g., 5000">
                                <small class="text-muted">Income threshold for eligibility</small>
                                @error('max_monthly_income')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- INCOME CATEGORIES --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Income Categories</label>
                            @php
                                $selectedIncomeCategories = old('income_categories', $criteria->income_categories ?? []);
                                if (!is_array($selectedIncomeCategories)) {
                                    $selectedIncomeCategories = [];
                                }
                            @endphp
                            <div class="row g-2">
                                @foreach (['B40' => 'B40 (Low Income)', 'M40' => 'M40 (Middle Income)', 'T20' => 'T20 (High Income)'] as $value => $label)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="checkbox" 
                                                name="income_categories[]" 
                                                value="{{ $value }}" 
                                                id="income_{{ $value }}"
                                                {{ in_array($value, $selectedIncomeCategories) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="income_{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Select income groups eligible or prioritised</small>
                        </div>

                        {{-- STUDY LEVELS --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Study Levels</label>
                            @php
                                $selectedStudyPaths = old('study_paths', $criteria->study_paths ?? []);
                                if (!is_array($selectedStudyPaths)) {
                                    $selectedStudyPaths = [];
                                }
                            @endphp
                            <div class="row g-2">
                                @foreach (['Foundation', 'Matriculation', 'Diploma', 'Degree', 'TVET'] as $level)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="checkbox" 
                                                name="study_paths[]" 
                                                value="{{ $level }}" 
                                                id="study_{{ $level }}"
                                                {{ in_array($level, $selectedStudyPaths) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="study_{{ $level }}">
                                                {{ $level }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Select all applicable study levels</small>
                        </div>

                        {{-- FIELDS OF STUDY --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fields of Study</label>
                            @php
                                $fieldOptions = ['Computer Science', 'Engineering', 'Business', 'Medicine', 'Education', 'TVET', 'Data Science', 'Finance', 'Accounting', 'Economics', 'Law', 'Actuarial Science', 'Mathematics', 'Statistics', 'Science', 'Physics', 'Chemistry', 'Biological Science', 'Pharmacy', 'Environmental Science', 'Architecture', 'Technical', 'Social Science', 'Communication', 'Hospitality', 'Anthropology', 'History', 'Linguistics', 'Performing Arts', 'Philosophy', 'Art & Design', 'Archaeology'];
                                $selectedFields = old('fields_of_study', $criteria->fields_of_study ?? []);
                                if (!is_array($selectedFields)) {
                                    $selectedFields = [];
                                }
                            @endphp
                            <div class="row g-2 fields-scroll">
                                @foreach ($fieldOptions as $field)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="checkbox" 
                                                name="fields_of_study[]" 
                                                value="{{ $field }}" 
                                                id="field_{{ Str::slug($field) }}"
                                                {{ in_array($field, $selectedFields) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="field_{{ Str::slug($field) }}">
                                                {{ $field }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Select fields of study covered by this scholarship</small>
                        </div>

                        {{-- CITIZENSHIP & STATE --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Citizenship Required</label>
                                <select name="citizenship_required" 
                                        class="form-select @error('citizenship_required') is-invalid @enderror">
                                    <option value="">No Restriction</option>
                                    <option value="Malaysia" {{ old('citizenship_required', $criteria->citizenship_required ?? '') == 'Malaysia' ? 'selected' : '' }}>
                                        Malaysia
                                    </option>
                                    <option value="International" {{ old('citizenship_required', $criteria->citizenship_required ?? '') == 'International' ? 'selected' : '' }}>
                                        International
                                    </option>
                                </select>
                                <small class="text-muted">Select the citizenship requirement for this scholarship</small>
                                @error('citizenship_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">State Requirement</label>
                                @php
                                    $states = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 'Labuan', 'Putrajaya'];
                                @endphp
                                <select name="state_requirement" 
                                        class="form-select @error('state_requirement') is-invalid @enderror">
                                    <option value="">No Restriction</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state }}" {{ old('state_requirement', $criteria->state_requirement ?? '') == $state ? 'selected' : '' }}>
                                            {{ $state }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select the state requirement (if any)</small>
                                @error('state_requirement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- AGE RANGE --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Minimum Age</label>
                                <input type="number" 
                                       name="min_age" 
                                       class="form-control @error('min_age') is-invalid @enderror" 
                                       value="{{ old('min_age', $criteria->min_age ?? '') }}" 
                                       placeholder="e.g., 17">
                                @error('min_age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Maximum Age</label>
                                <input type="number" 
                                       name="max_age" 
                                       class="form-control @error('max_age') is-invalid @enderror" 
                                       value="{{ old('max_age', $criteria->max_age ?? '') }}" 
                                       placeholder="e.g., 25">
                                @error('max_age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- PRIORITY OPTIONS --}}
                        <div class="mt-3">
                            <label class="form-label fw-semibold">Priority Options</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="hidden" name="bumiputera_required" value="0">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="bumiputera_required" 
                                               value="1" 
                                               id="bumiputera_required"
                                               {{ old('bumiputera_required', $criteria->bumiputera_required ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bumiputera_required">
                                            <i class="fas fa-star-of-life text-maroon me-1"></i>
                                            Bumiputera Required
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- RIGHT COLUMN - Application Details          --}}
            {{-- ============================================ --}}
            <div class="col-lg-4">

                {{-- APPLICATION DETAILS --}}
                <div class="card shadow-sm border-0 mb-4">
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
                            <input type="date" 
                                   name="deadline" 
                                   class="form-control @error('deadline') is-invalid @enderror" 
                                   value="{{ old('deadline', $deadline ? $deadline->format('Y-m-d') : '') }}">
                            <small class="text-muted">Leave empty for rolling deadline</small>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- APPLICATION LINK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Application Link</label>
                            <input type="url" 
                                   name="application_link" 
                                   class="form-control @error('application_link') is-invalid @enderror" 
                                   value="{{ old('application_link', $scholarship->application_link) }}" 
                                   placeholder="https://example.com/apply">
                            <small class="text-muted">Official application URL</small>
                            @error('application_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- OFFICIAL --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Official Scholarship</label>
                            <select name="is_official" class="form-select @error('is_official') is-invalid @enderror">
                                <option value="1" {{ old('is_official', $scholarship->is_official) ? 'selected' : '' }}>✅ Yes - Official Scholarship</option>
                                <option value="0" {{ !old('is_official', $scholarship->is_official) ? 'selected' : '' }}>❌ No - Third Party</option>
                            </select>
                            @error('is_official')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ACTIVE STATUS --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Active Status</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $scholarship->is_active) ? 'selected' : '' }}>🟢 Active - Visible to Students</option>
                                <option value="0" {{ !old('is_active', $scholarship->is_active) ? 'selected' : '' }}>🔴 Inactive - Hidden</option>
                            </select>
                            <small class="text-muted">Inactive scholarships won't appear in search results</small>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- HELPFUL TIPS --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Quick Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold d-block">Review All Fields</small>
                                <small class="text-muted">Double-check eligibility criteria for accuracy</small>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold d-block">Update Deadline</small>
                                <small class="text-muted">Keep deadline current for student visibility</small>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold d-block">Verify Link</small>
                                <small class="text-muted">Ensure application link is working properly</small>
                            </div>
                        </div>
                        <div class="d-flex">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold d-block">Set Inactive</small>
                                <small class="text-muted">Hide expired scholarships from search</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- FORM ACTIONS - MOVED TO BOTTOM               --}}
        {{-- ============================================ --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <a href="{{ route('admin.scholarships.show', $scholarship->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Update Scholarship
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* ============================================ */
    /* CARD STYLES                                 */
    /* ============================================ */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        padding: 14px 20px;
        border-bottom: 2px solid rgba(244, 197, 66, 0.3);
        background: rgba(122, 0, 25, 0.04);
    }

    .card-header h5,
    .card-header h6 {
        color: #7A0019;
        font-weight: 700;
        margin: 0;
    }

    .card-body {
        padding: 20px;
    }

    /* ============================================ */
    /* PAGE HEADER                                 */
    /* ============================================ */
    .text-maroon {
        color: #7A0019;
    }

    /* ============================================ */
    /* FORM STYLES                                 */
    /* ============================================ */
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-label .text-danger {
        color: #dc2626 !important;
        margin-left: 2px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.15);
        outline: none;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
    }

    .invalid-feedback {
        font-size: 0.8rem;
        color: #dc2626;
        margin-top: 4px;
    }

    .text-muted {
        color: #6b7280 !important;
        font-size: 0.78rem;
    }

    /* ============================================ */
    /* CHECKBOX STYLES                             */
    /* ============================================ */
    .form-check {
        padding-left: 1.8rem;
        margin-bottom: 0.3rem;
    }

    .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        margin-top: 0.15rem;
        margin-left: -1.8rem;
        border-radius: 4px;
        border: 2px solid #d1d5db;
        transition: all 0.2s ease;
    }

    .form-check-input:checked {
        background-color: #7A0019;
        border-color: #7A0019;
    }

    .form-check-input:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 0.2rem rgba(244, 197, 66, 0.2);
    }

    .form-check-label {
        font-size: 0.88rem;
        color: #374151;
    }

    /* ============================================ */
    /* ALERT STYLES                                */
    /* ============================================ */
    .alert-danger {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: none;
        border-left: 4px solid #dc2626;
        border-radius: 12px;
        color: #991b1b;
        padding: 16px 20px;
    }

    .alert-danger ul {
        padding-left: 20px;
        margin-top: 6px;
    }

    .alert-danger li {
        margin-bottom: 2px;
    }

    /* ============================================ */
    /* BUTTON STYLES                               */
    /* ============================================ */
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.25);
        background: linear-gradient(115deg, #4e0010, #7A0019);
        color: white;
    }

    .btn-outline-secondary {
        border: 2px solid #d1d5db;
        color: #6b7280;
        background: transparent;
        padding: 0.65rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-outline-secondary:hover {
        background: #6b7280;
        color: white;
        border-color: #6b7280;
        transform: translateY(-2px);
    }

    /* ============================================ */
    /* FIELDS SCROLLABLE                           */
    /* ============================================ */
    .fields-scroll {
        max-height: 200px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .fields-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .fields-scroll::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 10px;
    }

    .fields-scroll::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .fields-scroll::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* ============================================ */
    /* RESPONSIVE                                  */
    /* ============================================ */
    @media (max-width: 992px) {
        .d-flex.align-items-center.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .d-flex.align-items-center.justify-content-between .btn {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .card-header {
            padding: 12px 16px;
        }

        .card-body {
            padding: 16px;
        }

        .form-control,
        .form-select {
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .btn-primary,
        .btn-outline-secondary {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            width: 100%;
        }

        .form-check {
            padding-left: 1.6rem;
        }

        .form-check-input {
            width: 1rem;
            height: 1rem;
            margin-left: -1.6rem;
        }

        .form-check-label {
            font-size: 0.82rem;
        }

        .row.g-2 {
            --bs-gutter-y: 0.3rem;
        }

        .text-maroon {
            font-size: 1.1rem;
        }

        .d-flex.justify-content-between.align-items-center {
            flex-direction: column-reverse;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding: 0 8px !important;
        }

        .card-body {
            padding: 12px;
        }

        .fields-scroll {
            max-height: 150px;
        }
    }
</style>
@endpush

@endsection