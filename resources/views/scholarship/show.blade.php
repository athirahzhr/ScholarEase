@extends('layouts.app')

@section('title', $scholarship->title)

@section('content')

@php
    /* ===============================
       CODE → LABEL MAPPING
    =============================== */
    $academicMap = [
        'A1' => 'Excellent Academic Result',
        'A2' => 'Very Good Academic Result',
        'A3' => 'Good Academic Result',
        'A4' => 'Minimum Academic Requirement',
    ];

    $studyPathMap = [
        'C1' => 'Science Stream',
        'C2' => 'Arts Stream',
        'C3' => 'Technical / Vocational',
        'C4' => 'Religious Studies',
    ];
@endphp

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    {{-- Title --}}
                    <h2 class="mb-2">{{ $scholarship->title }}</h2>
                    <p class="text-muted mb-4">
                        <i class="fas fa-building me-2"></i>{{ $scholarship->provider }}
                    </p>

                    {{-- Description --}}
                    <div class="mb-4">
                        <h5 class="fw-bold mb-2">Description</h5>
                        <p class="text-secondary" style="line-height: 1.7;">
                            {{ $scholarship->description }}
                        </p>
                    </div>

                    {{-- ===============================
                       ELIGIBILITY (STRUCTURED)
                    =============================== --}}
                    <div class="mb-4">
                        <h5 class="fw-bold mb-2">Eligibility</h5>

                        @if($scholarship->eligibilityCriteria)
                            @php $e = $scholarship->eligibilityCriteria; @endphp

                            <ul class="list-group list-group-flush border rounded">

                                {{-- Academic Result --}}
                                @if(!is_null($e->min_spm_as) || !is_null($e->max_spm_as))
                                    <li class="list-group-item">
                                        Academic Result (SPM):
                                        {{ $e->min_spm_as ?? 'Any' }}
                                        –
                                        {{ $e->max_spm_as ?? 'Any' }} A’s
                                    </li>
                                @endif

                                {{-- Academic Category --}}
                                @if(!empty($e->academic_categories))
                                    <li class="list-group-item">
                                        Academic Category:
                                        {{ collect($e->academic_categories)
                                            ->map(fn($c) => $academicMap[$c] ?? $c)
                                            ->implode(', ') }}
                                    </li>
                                @endif

                                {{-- Required Subjects --}}
                                @if(!empty($e->required_subjects))
                                    <li class="list-group-item">
                                        Required Subjects:
                                        {{ implode(', ', $e->required_subjects) }}
                                    </li>
                                @endif

                                {{-- Study Path --}}
                                @if(!empty($e->study_paths))
                                    <li class="list-group-item">
                                        Study Path:
                                        {{ collect($e->study_paths)
                                            ->map(fn($c) => $studyPathMap[$c] ?? $c)
                                            ->implode(', ') }}
                                    </li>
                                @endif

                                {{-- Field of Study --}}
                                @if(!empty($e->fields_of_study))
                                    <li class="list-group-item">
                                        Field of Study:
                                        {{ implode(', ', $e->fields_of_study) }}
                                    </li>
                                @endif

                                {{-- Study Destination --}}
                                @if($e->study_destination && $e->study_destination !== 'Both')
                                    <li class="list-group-item">
                                        Study Destination:
                                        {{ $e->study_destination }}
                                    </li>
                                @endif

                                {{-- Income Category --}}
                                @if(!empty($e->income_categories))
                                    <li class="list-group-item">
                                        Income Category:
                                        {{ implode(', ', $e->income_categories) }}
                                    </li>
                                @endif

                                {{-- Max Monthly Income --}}
                                @if(!is_null($e->max_monthly_income))
                                    <li class="list-group-item">
                                        Maximum Monthly Income:
                                        RM {{ number_format($e->max_monthly_income, 2) }}
                                    </li>
                                @endif

                                {{-- Age --}}
                                @if(!is_null($e->min_age) || !is_null($e->max_age))
                                    <li class="list-group-item">
                                        Age Requirement:
                                        {{ $e->min_age ?? 'Any' }} – {{ $e->max_age ?? 'Any' }}
                                    </li>
                                @endif

                                {{-- Gender --}}
                                @if($e->gender_requirement && $e->gender_requirement !== 'Any')
                                    <li class="list-group-item">
                                        Gender Requirement:
                                        {{ $e->gender_requirement }}
                                    </li>
                                @endif

                                {{-- Citizenship --}}
                                @if(!empty($e->citizenship_required))
                                    <li class="list-group-item">
                                        Citizenship:
                                        {{ $e->citizenship_required }}
                                    </li>
                                @endif

                                {{-- State --}}
                                @if(!empty($e->state_requirement))
                                    <li class="list-group-item">
                                        State Requirement:
                                        {{ $e->state_requirement }}
                                    </li>
                                @endif

                                {{-- Bumiputera --}}
                                @if($e->bumiputera_required)
                                    <li class="list-group-item">
                                        Bumiputera Required
                                    </li>
                                @endif

                                {{-- Leadership --}}
                                @if($e->leadership_required)
                                    <li class="list-group-item">
                                        Leadership Experience Required
                                    </li>
                                @endif

                                {{-- Sports --}}
                                @if($e->sports_achievement)
                                    <li class="list-group-item">
                                        Sports Achievement Considered
                                    </li>
                                @endif

                                {{-- Community Service --}}
                                @if(!is_null($e->min_community_hours))
                                    <li class="list-group-item">
                                        Minimum Community Service:
                                        {{ $e->min_community_hours }} hours
                                    </li>
                                @endif

                                {{-- Bond --}}
                                @if($e->bond_required)
                                    <li class="list-group-item">
                                        Bond Required:
                                        {{ $e->bond_years ?? 'Specified' }} years
                                    </li>
                                @endif

                                {{-- Notes --}}
                                @if(!empty($e->notes))
                                    <li class="list-group-item">
                                        Additional Notes:
                                        {{ $e->notes }}
                                    </li>
                                @endif

                            </ul>
                        @else
                            <div class="border rounded p-3 bg-light text-muted">
                                Eligibility information not available.
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        @if($scholarship->application_link)
                            <a href="{{ $scholarship->application_link }}" target="_blank"
                               class="btn btn-success px-4">
                                <i class="fas fa-paper-plane me-2"></i>Apply Now
                            </a>
                        @endif

                        <form action="{{ route('bookmarks.toggle', ['id' => $scholarship->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary px-4">
                                <i class="fas fa-bookmark me-2"></i>Bookmark
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
