@extends('layouts.admin')

@section('title', 'Edit Scholarship')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <form method="POST" action="{{ route('admin.scholarships.update', $scholarship->id) }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Scholarship: {{ $scholarship->title }}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            {{-- ================= LEFT ================= --}}
                            <div class="col-md-8">

                                {{-- TITLE --}}
                                <div class="mb-3">
                                    <label class="form-label">Scholarship Title *</label>
                                    <input type="text"
                                           name="title"
                                           class="form-control"
                                           value="{{ old('title', $scholarship->title) }}"
                                           required>
                                </div>

                                {{-- PROVIDER --}}
                                <div class="mb-3">
                                    <label class="form-label">Provider *</label>
                                    <input type="text"
                                           name="provider"
                                           class="form-control"
                                           value="{{ old('provider', $scholarship->provider) }}"
                                           required>
                                </div>

                                {{-- DESCRIPTION --}}
                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea name="description"
                                              rows="4"
                                              class="form-control"
                                              required>{{ old('description', $scholarship->description) }}</textarea>
                                </div>

                                {{-- RAW ELIGIBILITY --}}
                                <div class="mb-3">
                                    <label class="form-label">Eligibility (Official / Raw) *</label>
                                    <textarea name="raw_eligibility"
                                              rows="4"
                                              class="form-control"
                                              required>{{ old('raw_eligibility', $scholarship->raw_eligibility) }}</textarea>
                                </div>

                            </div>

                            {{-- ================= RIGHT ================= --}}
                            <div class="col-md-4">

                                {{-- DEADLINE --}}
                                <div class="mb-3">
                                    <label class="form-label">Deadline</label>
                                    <input type="date"
                                           name="deadline"
                                           class="form-control"
                                           value="{{ optional($scholarship->deadline)->format('Y-m-d') }}">
                                </div>

                                {{-- APPLICATION LINK --}}
                                <div class="mb-3">
                                    <label class="form-label">Application Link</label>
                                    <input type="url"
                                           name="application_link"
                                           class="form-control"
                                           value="{{ old('application_link', $scholarship->application_link) }}">
                                </div>

                                <hr>

                                {{-- MIN SPM AS --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        Minimum SPM A’s:
                                        <strong id="asValue">
                                            {{ $scholarship->eligibilityCriteria->min_spm_as ?? 0 }}
                                        </strong>
                                    </label>

                                    <input type="range"
                                           min="0"
                                           max="12"
                                           name="min_spm_as"
                                           value="{{ old('min_spm_as', $scholarship->eligibilityCriteria->min_spm_as ?? 0) }}"
                                           class="form-range"
                                           oninput="document.getElementById('asValue').innerText=this.value">
                                </div>

                                {{-- INCOME CATEGORY --}}
                                <label class="form-label">Income Category</label>
                                @php
                                    $selectedIncome = old(
                                        'income_categories',
                                        $scholarship->eligibilityCriteria->income_categories ?? []
                                    );
                                @endphp

                                @foreach(['B1'=>'B40','B3'=>'M40','B4'=>'T20'] as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="income_categories[]"
                                               value="{{ $key }}"
                                               {{ in_array($key, $selectedIncome) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $label }}</label>
                                    </div>
                                @endforeach

                                                                <label class="form-label">Study Path</label>
                                @foreach(['C1'=>'Pre-University','C2'=>'Diploma','C3'=>'Degree','C4'=>'TVET'] as $k=>$v)
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="study_paths[]"
                                        value="{{ $k }}"
                                        {{ in_array($k, $scholarship->eligibilityCriteria->study_paths ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $v }}</label>
                                </div>
                                @endforeach


                                <hr>

                                {{-- ACTIVE STATUS --}}
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $scholarship->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Active Scholarship</label>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('admin.scholarships.show', $scholarship->id) }}"
                           class="btn btn-secondary">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Update Scholarship
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>
@endsection
