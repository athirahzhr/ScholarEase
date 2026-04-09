@extends('layouts.admin')

@section('title', 'Add New Scholarship')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Add New Scholarship
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.scholarships.store') }}">
                        @csrf

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            {{-- LEFT : BASIC INFO --}}
                            <div class="col-md-8">

                                {{-- Basic Information --}}
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">

                                        <div class="mb-3">
                                            <label class="form-label">Scholarship Title *</label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Provider / Organization *</label>
                                            <input type="text" name="provider" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Description *</label>
                                            <textarea name="description" rows="4" class="form-control" required></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Raw Eligibility (Text)</label>
                                            <textarea name="raw_eligibility" rows="4" class="form-control"
                                                placeholder="Eligibility description from website / manual input"></textarea>
                                        </div>

                                    </div>
                                </div>

                                {{-- Eligibility Criteria --}}
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Eligibility Criteria</h6>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Min SPM A's</label>
                                                <input type="number" name="min_spm_as" class="form-control">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Max SPM A's</label>
                                                <input type="number" name="max_spm_as" class="form-control">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Academic Categories</label>
                                            <select name="academic_categories[]" class="form-select" multiple>
                                                <option value="A1">A1 (0–3 A's)</option>
                                                <option value="A2">A2 (4–6 A's)</option>
                                                <option value="A3">A3 (7–9 A's)</option>
                                                <option value="A4">A4 (10+ A's)</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Income Categories</label>
                                            <select name="income_categories[]" class="form-select" multiple>
                                                <option value="B40">B40</option>
                                                <option value="M40">M40</option>
                                                <option value="T20">T20</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Study Paths</label>
                                            <select name="study_paths[]" class="form-select" multiple>
                                                <option value="Pre-University">Pre-University</option>
                                                <option value="Diploma">Diploma</option>
                                                <option value="Degree">Degree</option>
                                                <option value="TVET">TVET</option>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Max Monthly Income (RM)</label>
                                                <input type="number" step="0.01" name="max_monthly_income" class="form-control">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Gender Requirement</label>
                                                <select name="gender_requirement" class="form-select">
                                                    <option value="Any">Any</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            {{-- RIGHT : APPLICATION --}}
                            <div class="col-md-4">

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Application Details</h6>
                                    </div>
                                    <div class="card-body">

                                        <div class="mb-3">
                                            <label class="form-label">Application Deadline</label>
                                            <input type="date" name="deadline" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Application Link</label>
                                            <input type="url" name="application_link" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Official Scholarship</label>
                                            <select name="is_official" class="form-select">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Active Status</label>
                                            <select name="is_active" class="form-select">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.scholarships.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Scholarship
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
