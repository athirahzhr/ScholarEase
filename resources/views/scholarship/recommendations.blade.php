@extends('layouts.app')

@section('title', 'Scholarship Recommendations')

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

    .recommendations-header {
        background: linear-gradient(135deg, var(--cream), var(--cream-dark));
        padding: 2rem;
        border-radius: 24px;
        margin-bottom: 2rem;
        border-left: 4px solid var(--gold);
    }

    .recommendations-header h2 {
        color: var(--maroon);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .recommendations-header p {
        color: var(--gray-600);
        margin-bottom: 0;
    }

    .recommendation-card {
        display: flex;
        gap: 24px;
        background: white;
        border-radius: 24px;
        padding: 28px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .recommendation-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--maroon));
    }

    .recommendation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }

    .recommendation-icon {
        font-size: 48px;
        display: flex;
        align-items: flex-start;
    }

    .recommendation-content {
        flex: 1;
    }

    .scholarship-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--maroon);
    }

    .provider-name {
        color: var(--gray-600);
        font-weight: 600;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .provider-name i { color: var(--gold); }

    .recommendation-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .tag {
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .score-tag        { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .success-tag      { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
    .primary-tag      { background: linear-gradient(135deg, #ede9fe, #c4b5fd); color: #6d28d9; }
    .warning-tag      { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }

    .scholarship-description {
        color: var(--gray-600);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    /* ── Breakdown ─────────────────────────────────────────────────────────── */
    .breakdown-list {
        margin-top: 14px;
        margin-bottom: 18px;
        background: #f9fafb;
        padding: 14px 16px;
        border-radius: 16px;
    }

    .breakdown-item {
        font-size: 0.85rem;
        color: var(--gray-600);
        margin-bottom: 10px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .breakdown-item:last-child { margin-bottom: 0; }

    /* icon column — fixed width so text aligns */
    .breakdown-icon {
        width: 18px;
        flex-shrink: 0;
        margin-top: 2px;
        font-size: 0.9rem;
    }

    .breakdown-icon.pass    { color: #10b981; }   /* green  */
    .breakdown-icon.partial { color: #f59e0b; }   /* amber  */
    .breakdown-icon.fail    { color: #ef4444; }   /* red    */

    .breakdown-label {
        font-weight: 600;
        color: var(--gray-800);
        white-space: nowrap;
        min-width: 130px;
    }

    .breakdown-detail {
        color: var(--gray-600);
    }

    /* inline score pill inside breakdown */
    .breakdown-score {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 1px 8px;
        border-radius: 20px;
        margin-left: 6px;
    }

    .score-full    { background: #d1fae5; color: #065f46; }
    .score-partial { background: #fef3c7; color: #92400e; }
    .score-zero    { background: #fee2e2; color: #991b1b; }

    /* ── Score bar ─────────────────────────────────────────────────────────── */
    .score-bar-wrap {
        margin-bottom: 16px;
    }

    .score-bar-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .score-bar-bg {
        background: #e5e7eb;
        border-radius: 99px;
        height: 8px;
        overflow: hidden;
    }

    .score-bar-fill {
        height: 100%;
        border-radius: 99px;
        width: var(--bar-width, 0%);
        transition: width 0.6s ease;
    }

    .fill-high    { background: linear-gradient(90deg, #10b981, #34d399); }
    .fill-medium  { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
    .fill-low     { background: linear-gradient(90deg, #ef4444, #f87171); }

    /* ── Footer ────────────────────────────────────────────────────────────── */
    .recommendation-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .deadline-text {
        color: var(--gray-600);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .deadline-text i { color: var(--gold); }

    .recommendation-actions { display: flex; gap: 10px; }

    .btn-primary {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        border: none;
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }

    .btn-outline-secondary {
        border: 2px solid #e5e7eb;
        color: var(--gray-600);
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: transparent;
    }

    .btn-outline-secondary:hover {
        border-color: var(--maroon);
        color: var(--maroon);
        transform: translateY(-2px);
    }

    .btn-outline-primary {
        border: 2px solid var(--maroon);
        color: var(--maroon);
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: transparent;
    }

    .btn-outline-primary:hover {
        background: var(--maroon);
        color: white;
        transform: translateY(-2px);
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-left: 4px solid #10b981;
        border-radius: 16px;
        color: #065f46;
        padding: 1rem;
    }

    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: none;
        border-left: 4px solid #f59e0b;
        border-radius: 16px;
        color: #92400e;
        padding: 1rem;
    }

    .alert-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border: none;
        border-left: 4px solid #3b82f6;
        border-radius: 16px;
        color: #1e40af;
        padding: 1rem;
    }

    .alert-warning a, .alert-info a { color: var(--maroon); font-weight: 600; text-decoration: none; }
    .alert-warning a:hover, .alert-info a:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        .recommendation-card { flex-direction: column; padding: 20px; gap: 12px; }
        .recommendation-icon { font-size: 36px; }
        .scholarship-title   { font-size: 1.2rem; }
        .recommendation-footer { flex-direction: column; align-items: flex-start; }
        .recommendation-actions { width: 100%; justify-content: flex-start; }
        .btn-primary, .btn-outline-secondary, .btn-outline-primary { padding: 0.4rem 1rem; font-size: 0.8rem; }
        .breakdown-label { min-width: 110px; }
    }
</style>

<div class="container py-4">
    <div class="recommendations-header" data-aos="fade-up">
        <h2>
            <i class="fas fa-graduation-cap me-2" style="color: var(--gold);"></i>
            Scholarship Recommendations
        </h2>
        <p>Personalised scholarship matches based on your academic profile and preferences</p>
    </div>

    @if(session('success'))
        <div class="alert-success mb-4" data-aos="fade-down">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(!auth()->user()->profile)
        <div class="alert-warning mb-4" data-aos="fade-down">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Please complete your profile to get personalised recommendations.
            <a href="{{ route('scholarship.finder') }}" class="ms-2">
                <i class="fas fa-arrow-right"></i> Complete Profile
            </a>
        </div>
    @else
        <div class="alert-success mb-4" data-aos="fade-down">
            <i class="fas fa-chart-line me-2"></i>
            Scholarships below are matched based on your academic results, income category, study path, and eligibility criteria.
        </div>
    @endif

    <div class="row">
        @forelse($results as $scholarship)
            @php
                $matchScore    = $scholarship->match_score    ?? 0;
                $matchLevel    = $scholarship->match_level    ?? 'Low Match';
                $breakdown     = $scholarship->match_breakdown ?? [];

                // Scored criteria detail strings (from PHP service)
                $spmData    = $breakdown['spm']    ?? [];
                $incomeData = $breakdown['income'] ?? [];
                $bonusData  = $breakdown['bonus']  ?? [];

                // Hard filter results (bool)
                $passedCitizenship = $breakdown['citizenship']  ?? true;
                $passedBumiputera  = $breakdown['bumiputera']   ?? true;
                $passedStudyLevel  = $breakdown['study_level']  ?? true;
                $passedField       = $breakdown['field']        ?? true;
                $passedAge         = $breakdown['age']          ?? true;

                // SPM status
                $spmEarned  = $spmData['earned'] ?? 0;
                $spmMax     = $spmData['max']    ?? 0;
                $spmDetail  = $spmData['detail'] ?? '';
                $spmFull    = $spmMax > 0 && $spmEarned === $spmMax;
                $spmZero    = $spmEarned === 0;

                // Income status
                $incomeEarned = $incomeData['earned'] ?? 0;
                $incomeMax    = $incomeData['max']    ?? 0;
                $incomeDetail = $incomeData['detail'] ?? '';
                $incomeFull   = $incomeMax > 0 && $incomeEarned === $incomeMax;
                $incomeZero   = $incomeEarned === 0;

                // Bonus
                $bonusEarned  = $bonusData['earned']  ?? 0;
                $bonusDetails = $bonusData['detail']  ?? [];

                // Bar colour
                $barClass = match(true) {
                    $matchScore >= 80 => 'fill-high',
                    $matchScore >= 60 => 'fill-medium',
                    default           => 'fill-low',
                };
            @endphp

            <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                <div class="recommendation-card">
                    <div class="recommendation-icon">🎓</div>

                    <div class="recommendation-content">

                        {{-- Title & Provider --}}
                        <h3 class="scholarship-title">{{ $scholarship->title }}</h3>
                        <div class="provider-name">
                            <i class="fas fa-building"></i>
                            {{ $scholarship->provider }}
                        </div>

                        {{-- Match badge --}}
                        <div class="recommendation-tags">
                            <span class="tag score-tag">
                                <i class="fas fa-chart-simple me-1"></i>
                                {{ $matchScore }}% — {{ $matchLevel }}
                            </span>

                            @if($matchLevel === 'High Match')
                                <span class="tag success-tag"><i class="fas fa-star me-1"></i> Highly Recommended</span>
                            @elseif($matchLevel === 'Medium Match')
                                <span class="tag primary-tag"><i class="fas fa-thumbs-up me-1"></i> Good Match</span>
                            @else
                                <span class="tag warning-tag"><i class="fas fa-chart-line me-1"></i> Low Match</span>
                            @endif
                        </div>

                        {{-- Score bar --}}
                        <div class="score-bar-wrap">
                            <div class="score-bar-label">
                                <span>Match Score</span>
                                <span>{{ $matchScore }}/100</span>
                            </div>
                            <div class="score-bar-bg">
                                <div class="score-bar-fill {{ $barClass }}" style="--bar-width: {{ $matchScore }}%"></div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <p class="scholarship-description">
                            {{ \Illuminate\Support\Str::limit(strip_tags($scholarship->description), 180) }}
                        </p>

                        {{-- ── Breakdown ──────────────────────────────────── --}}
                        <div class="breakdown-list">

                            {{-- 1. SPM (scored) --}}
                            @if($spmMax > 0)
                            <div class="breakdown-item">
                                <i class="fas breakdown-icon
                                    {{ $spmFull ? 'fa-check-circle pass' : ($spmZero ? 'fa-times-circle fail' : 'fa-exclamation-circle partial') }}">
                                </i>
                                <span class="breakdown-label">SPM Result</span>
                                <span class="breakdown-detail">
                                    {{ $spmDetail }}
                                    <span class="breakdown-score {{ $spmFull ? 'score-full' : ($spmZero ? 'score-zero' : 'score-partial') }}">
                                        {{ $spmEarned }}/{{ $spmMax }} pts
                                    </span>
                                </span>
                            </div>
                            @endif

                            {{-- 2. Monthly Income (scored) --}}
                            @if($incomeMax > 0)
                            <div class="breakdown-item">
                                <i class="fas breakdown-icon
                                    {{ $incomeFull ? 'fa-check-circle pass' : ($incomeZero ? 'fa-times-circle fail' : 'fa-exclamation-circle partial') }}">
                                </i>
                                <span class="breakdown-label">Monthly Income</span>
                                <span class="breakdown-detail">
                                    {{ $incomeDetail }}
                                    <span class="breakdown-score {{ $incomeFull ? 'score-full' : ($incomeZero ? 'score-zero' : 'score-partial') }}">
                                        {{ $incomeEarned }}/{{ $incomeMax }} pts
                                    </span>
                                </span>
                            </div>
                            @endif

                            {{-- 3. Hard filter — Study Level --}}
                            <div class="breakdown-item">
                                <i class="fas breakdown-icon {{ $passedStudyLevel ? 'fa-check-circle pass' : 'fa-times-circle fail' }}"></i>
                                <span class="breakdown-label">Study Level</span>
                                <span class="breakdown-detail">
                                    {{ $passedStudyLevel ? 'Eligible' : 'Not eligible for this study level' }}
                                </span>
                            </div>

                            {{-- 4. Hard filter — Field of Study --}}
                            <div class="breakdown-item">
                                <i class="fas breakdown-icon {{ $passedField ? 'fa-check-circle pass' : 'fa-times-circle fail' }}"></i>
                                <span class="breakdown-label">Field of Study</span>
                                <span class="breakdown-detail">
                                    {{ $passedField ? 'Eligible' : 'Your field is not listed for this scholarship' }}
                                </span>
                            </div>

                            {{-- 5. Hard filter — Age --}}
                            <div class="breakdown-item">
                                <i class="fas breakdown-icon {{ $passedAge ? 'fa-check-circle pass' : 'fa-times-circle fail' }}"></i>
                                <span class="breakdown-label">Age</span>
                                <span class="breakdown-detail">
                                    {{ $passedAge ? 'Within age requirement' : 'Outside the required age range' }}
                                </span>
                            </div>

                            {{-- 6. Hard filter — Citizenship --}}
                            <div class="breakdown-item">
                                <i class="fas breakdown-icon {{ $passedCitizenship ? 'fa-check-circle pass' : 'fa-times-circle fail' }}"></i>
                                <span class="breakdown-label">Citizenship</span>
                                <span class="breakdown-detail">
                                    {{ $passedCitizenship ? 'Eligible' : 'Citizenship requirement not met' }}
                                </span>
                            </div>

                            {{-- 7. Hard filter — Bumiputera --}}
                            <div class="breakdown-item">
                                <i class="fas breakdown-icon {{ $passedBumiputera ? 'fa-check-circle pass' : 'fa-times-circle fail' }}"></i>
                                <span class="breakdown-label">Bumiputera</span>
                                <span class="breakdown-detail">
                                    {{ $passedBumiputera ? 'Eligible' : 'This scholarship requires Bumiputera status' }}
                                </span>
                            </div>

                            {{-- 8. Bonus (only show if any bonus was earned) --}}
                            @if($bonusEarned > 0)
                            <div class="breakdown-item">
                                <i class="fas fa-plus-circle breakdown-icon pass"></i>
                                <span class="breakdown-label">Bonus</span>
                                <span class="breakdown-detail">
                                    {{ implode(', ', $bonusDetails) }}
                                    <span class="breakdown-score score-full">+{{ $bonusEarned }} pts</span>
                                </span>
                            </div>
                            @endif

                        </div>
                        {{-- ── End Breakdown ──────────────────────────────── --}}

                        {{-- Footer --}}
                        <div class="recommendation-footer">
                            <div class="deadline-text">
                                <i class="fas fa-calendar-alt"></i>
                                Deadline:
                                {{ $scholarship->deadline
                                    ? \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y')
                                    : 'Rolling / Not specified' }}
                            </div>

                            <div class="recommendation-actions">
                                @if($scholarship->application_link)
                                    <a href="{{ $scholarship->application_link }}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> Apply
                                    </a>
                                @endif

                                <a href="{{ route('scholarships.show', $scholarship->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-info-circle me-1"></i> Details
                                </a>

                                <form action="{{ route('bookmarks.toggle', $scholarship->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-bookmark me-1"></i> Save
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert-info text-center py-5" data-aos="fade-up">
                    <i class="fas fa-search fa-3x mb-3" style="display: block;"></i>
                    <h5 class="mb-2">No Scholarships Found</h5>
                    <p class="mb-0">No scholarships match your current criteria. Try updating your profile or check back later.</p>
                    <a href="{{ route('scholarship.finder') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-edit me-2"></i> Update Profile
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
@endpush
@endsection