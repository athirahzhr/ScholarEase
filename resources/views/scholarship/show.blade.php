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

    .details-container {
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        min-height: calc(100vh - 200px);
        padding: 3rem 0;
    }

    .scholarship-card {
        background: white;
        border-radius: 28px;
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .scholarship-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 2rem;
        border-bottom: 3px solid var(--gold);
    }

    .scholarship-header h2 {
        color: white;
        font-weight: 800;
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }

    .scholarship-header .provider {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .scholarship-header .provider i {
        color: var(--gold);
    }

    .scholarship-body {
        padding: 2rem;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--maroon);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gold);
        display: inline-block;
    }

    .description-text {
        color: var(--gray-600);
        line-height: 1.7;
        font-size: 1rem;
    }

    .eligibility-list {
        list-style: none;
        padding: 0;
        margin: 0;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        overflow: hidden;
    }

    .eligibility-item {
        padding: 1rem 1.25rem;
        background: white;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        transition: all 0.2s ease;
    }

    .eligibility-item:last-child {
        border-bottom: none;
    }

    .eligibility-item:hover {
        background: rgba(244, 197, 66, 0.08);
    }

    .eligibility-icon {
        width: 24px;
        color: var(--maroon);
        margin-top: 2px;
    }

    .eligibility-label {
        font-weight: 700;
        color: var(--gray-800);
        min-width: 180px;
    }

    .eligibility-value {
        color: var(--gray-600);
        flex: 1;
    }

    .eligibility-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin: 2px 4px 2px 0;
    }

    .badge-maroon {
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(122,0,25,0.05));
        color: var(--maroon);
        border: 1px solid rgba(122,0,25,0.2);
    }

    .badge-gold {
        background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.08));
        color: #92400e;
        border: 1px solid rgba(244,197,66,0.3);
    }

    .badge-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .badge-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }

    .btn-apply {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        border: none;
        border-radius: 60px;
        padding: 0.75rem 2rem;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-apply:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.4);
        color: white;
    }

    .btn-bookmark {
        background: transparent;
        border: 2px solid var(--maroon);
        color: var(--maroon);
        border-radius: 60px;
        padding: 0.75rem 2rem;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .btn-bookmark:hover {
        background: var(--maroon);
        color: white;
        transform: translateY(-2px);
    }

    .empty-eligibility {
        background: #f9fafb;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        border: 1px solid #e5e7eb;
    }

    @media (max-width: 768px) {
        .scholarship-header {
            padding: 1.5rem;
        }
        
        .scholarship-header h2 {
            font-size: 1.4rem;
        }
        
        .scholarship-body {
            padding: 1.5rem;
        }
        
        .eligibility-item {
            flex-direction: column;
            gap: 6px;
        }
        
        .eligibility-label {
            min-width: auto;
        }
        
        .btn-apply, .btn-bookmark {
            padding: 0.6rem 1.5rem;
            font-size: 0.9rem;
        }
    }
</style>

<div class="details-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="scholarship-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="scholarship-header">
                        <h2>{{ $scholarship->title }}</h2>
                        <div class="provider">
                            <i class="fas fa-building"></i>
                            {{ $scholarship->provider }}
                        </div>
                    </div>

                    <div class="scholarship-body">
                        {{-- Description Section --}}
                        <div class="mb-5">
                            <h5 class="section-title">
                                <i class="fas fa-align-left me-2"></i> Description
                            </h5>
                            <p class="description-text">
                                {{ $scholarship->description }}
                            </p>
                        </div>

                        {{-- Eligibility Section --}}
                        <div class="mb-5">
                            <h5 class="section-title">
                                <i class="fas fa-clipboard-list me-2"></i> Eligibility Criteria
                            </h5>

                            @if($scholarship->eligibilityCriteria)
                                @php $e = $scholarship->eligibilityCriteria; @endphp

                                <div class="eligibility-list">
                                    {{-- Academic Result --}}
                                    @if(!is_null($e->min_spm_as) || !is_null($e->max_spm_as))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="eligibility-label">Academic Result (SPM):</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-maroon">
                                                    {{ $e->min_spm_as ?? 'Any' }} – {{ $e->max_spm_as ?? 'Any' }} A's
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Academic Category --}}
                                    @if(!empty($e->academic_categories))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div class="eligibility-label">Academic Category:</div>
                                            <div class="eligibility-value">
                                                @foreach($e->academic_categories as $cat)
                                                    <span class="eligibility-badge badge-gold">{{ $academicMap[$cat] ?? $cat }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Required Subjects --}}
                                    @if(!empty($e->required_subjects))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div class="eligibility-label">Required Subjects:</div>
                                            <div class="eligibility-value">
                                                @foreach($e->required_subjects as $subject)
                                                    <span class="eligibility-badge badge-info">{{ $subject }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Study Path --}}
                                    @if(!empty($e->study_paths))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-road"></i>
                                            </div>
                                            <div class="eligibility-label">Study Path:</div>
                                            <div class="eligibility-value">
                                                @foreach($e->study_paths as $path)
                                                    <span class="eligibility-badge badge-maroon">{{ $studyPathMap[$path] ?? $path }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Field of Study --}}
                                    @if(!empty($e->fields_of_study))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-book-open"></i>
                                            </div>
                                            <div class="eligibility-label">Field of Study:</div>
                                            <div class="eligibility-value">
                                                @foreach($e->fields_of_study as $field)
                                                    <span class="eligibility-badge badge-gold">{{ $field }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Study Destination --}}
                                    @if($e->study_destination && $e->study_destination !== 'Both')
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-globe"></i>
                                            </div>
                                            <div class="eligibility-label">Study Destination:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-info">{{ $e->study_destination }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Income Category --}}
                                    @if(!empty($e->income_categories))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <div class="eligibility-label">Income Category:</div>
                                            <div class="eligibility-value">
                                                @foreach($e->income_categories as $cat)
                                                    <span class="eligibility-badge badge-success">{{ $cat }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Max Monthly Income --}}
                                    @if(!is_null($e->max_monthly_income))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="eligibility-label">Maximum Monthly Income:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-success">RM {{ number_format($e->max_monthly_income, 2) }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Age --}}
                                    @if(!is_null($e->min_age) || !is_null($e->max_age))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-birthday-cake"></i>
                                            </div>
                                            <div class="eligibility-label">Age Requirement:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-maroon">{{ $e->min_age ?? 'Any' }} – {{ $e->max_age ?? 'Any' }} years</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Citizenship --}}
                                    @if(!empty($e->citizenship_required))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-passport"></i>
                                            </div>
                                            <div class="eligibility-label">Citizenship:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-info">{{ $e->citizenship_required }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- State --}}
                                    @if(!empty($e->state_requirement))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="eligibility-label">State Requirement:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-maroon">{{ $e->state_requirement }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Bumiputera --}}
                                    @if($e->bumiputera_required)
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-star-of-life"></i>
                                            </div>
                                            <div class="eligibility-label">Bumiputera Status:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-success">Required</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Leadership --}}
                                    @if($e->leadership_required)
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-trophy"></i>
                                            </div>
                                            <div class="eligibility-label">Leadership:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-gold">Experience Required</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Sports --}}
                                    @if($e->sports_achievement)
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-futbol"></i>
                                            </div>
                                            <div class="eligibility-label">Sports Achievement:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-info">Considered</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Community Service --}}
                                    @if(!is_null($e->min_community_hours))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-hand-holding-heart"></i>
                                            </div>
                                            <div class="eligibility-label">Community Service:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-success">Minimum {{ $e->min_community_hours }} hours</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Bond --}}
                                    @if($e->bond_required)
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-link"></i>
                                            </div>
                                            <div class="eligibility-label">Bond Required:</div>
                                            <div class="eligibility-value">
                                                <span class="eligibility-badge badge-warning">{{ $e->bond_years ?? 'Specified' }} years</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Notes --}}
                                    @if(!empty($e->notes))
                                        <div class="eligibility-item">
                                            <div class="eligibility-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="eligibility-label">Additional Notes:</div>
                                            <div class="eligibility-value">
                                                <span class="text-muted">{{ $e->notes }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="empty-eligibility">
                                    <i class="fas fa-clipboard-list fa-3x mb-3" style="color: #d1d5db;"></i>
                                    <p class="text-muted mb-0">Eligibility information not available for this scholarship.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex flex-wrap gap-3 mt-4 pt-2">
                            @if($scholarship->application_link)
                                <a href="{{ $scholarship->application_link }}" target="_blank" class="btn-apply">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </a>
                            @endif

                            <form action="{{ route('bookmarks.toggle', ['id' => $scholarship->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-bookmark">
                                    <i class="fas fa-bookmark"></i> Bookmark Scholarship
                                </button>
                            </form>
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