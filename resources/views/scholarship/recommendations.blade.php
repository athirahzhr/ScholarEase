@extends('layouts.app')

@section('title', 'Scholarship Recommendations')

@section('content')

<style>
    :root {
        --maroon: #7A0019;
        --maroon-dark: #4e0010;
        --gold: #F4C542;
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

    .recommendations-header h2 { color: var(--maroon); font-weight: 700; margin-bottom: 0.5rem; }
    .recommendations-header p  { color: var(--gray-600); margin-bottom: 0; }

    .recommendation-card {
        display: flex;
        gap: 24px;
        background: white;
        border-radius: 24px;
        padding: 28px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .recommendation-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--maroon));
    }

    .recommendation-card.general-match::before {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .recommendation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0,0,0,.15);
    }

    .recommendation-icon    { font-size: 48px; display: flex; align-items: flex-start; }
    .recommendation-content { flex: 1; }
    .scholarship-title      { font-size: 1.5rem; font-weight: 700; margin-bottom: 6px; color: var(--maroon); }
    .provider-name          { color: var(--gray-600); font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .provider-name i        { color: var(--gold); }

    .recommendation-tags    { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 18px; }
    .tag                    { padding: 6px 14px; border-radius: 40px; font-size: 0.8rem; font-weight: 600; }
    .tag-priority           { background: linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46; }
    .tag-general            { background: linear-gradient(135deg,#fef3c7,#fde68a); color:#92400e; }
    .tag-eligible           { background: linear-gradient(135deg,#dbeafe,#bfdbfe); color:#1e40af; }

    .scholarship-description { color: var(--gray-600); line-height: 1.6; margin-bottom: 20px; }

    .breakdown-list         { margin: 14px 0 18px; background: #f9fafb; padding: 14px 16px; border-radius: 16px; }
    .breakdown-item         { font-size: 0.85rem; color: var(--gray-600); margin-bottom: 8px; display: flex; align-items: flex-start; gap: 10px; }
    .breakdown-item:last-child { margin-bottom: 0; }
    .breakdown-icon         { width: 18px; flex-shrink: 0; margin-top: 2px; font-size: 0.9rem; }
    .breakdown-icon.pass    { color: #10b981; }
    .breakdown-icon.fail    { color: #ef4444; }
    .breakdown-label        { font-weight: 600; color: var(--gray-800); min-width: 130px; white-space: nowrap; }
    .breakdown-detail       { color: var(--gray-600); }

    .general-notice {
        background: linear-gradient(135deg,#fef3c7,#fde68a);
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 16px;
        font-size: 0.85rem;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .group-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 2rem 0 1rem;
    }

    .group-divider span     { font-size: 0.85rem; font-weight: 600; color: var(--gray-600); white-space: nowrap; }
    .group-divider::before,
    .group-divider::after   { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

    .recommendation-footer  { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .deadline-text          { color: var(--gray-600); font-size: 0.9rem; display: flex; align-items: center; gap: 8px; }
    .deadline-text i        { color: var(--gold); }
    .recommendation-actions { display: flex; gap: 10px; }

    .btn-primary {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        border: none; border-radius: 40px;
        padding: 0.5rem 1.2rem; font-weight: 600; font-size: 0.85rem;
        transition: all 0.3s ease; color: white;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(122,0,25,.3); }

    .btn-outline-secondary {
        border: 2px solid #e5e7eb; color: var(--gray-600); border-radius: 40px;
        padding: 0.5rem 1.2rem; font-size: 0.85rem; font-weight: 600;
        transition: all 0.3s ease; background: transparent;
    }
    .btn-outline-secondary:hover { border-color: var(--maroon); color: var(--maroon); transform: translateY(-2px); }

    .btn-outline-primary {
        border: 2px solid var(--maroon); color: var(--maroon); border-radius: 40px;
        padding: 0.5rem 1.2rem; font-size: 0.85rem; font-weight: 600;
        transition: all 0.3s ease; background: transparent;
    }
    .btn-outline-primary:hover { background: var(--maroon); color: white; transform: translateY(-2px); }

    .alert-success { background: linear-gradient(135deg,#d1fae5,#a7f3d0); border: none; border-left: 4px solid #10b981; border-radius: 16px; color: #065f46; padding: 1rem; }
    .alert-warning { background: linear-gradient(135deg,#fef3c7,#fde68a); border: none; border-left: 4px solid #f59e0b; border-radius: 16px; color: #92400e; padding: 1rem; }
    .alert-info    { background: linear-gradient(135deg,#dbeafe,#bfdbfe); border: none; border-left: 4px solid #3b82f6; border-radius: 16px; color: #1e40af; padding: 1rem; }
    .alert-warning a, .alert-info a { color: var(--maroon); font-weight: 600; text-decoration: none; }

    @media (max-width: 768px) {
        .recommendation-card    { flex-direction: column; padding: 20px; gap: 12px; }
        .recommendation-icon    { font-size: 36px; }
        .scholarship-title      { font-size: 1.2rem; }
        .recommendation-footer  { flex-direction: column; align-items: flex-start; }
        .recommendation-actions { width: 100%; justify-content: flex-start; }
        .breakdown-label        { min-width: 110px; }
    }
</style>

<div class="container py-4">

    <div class="recommendations-header" data-aos="fade-up">
        <h2>
            <i class="fas fa-graduation-cap me-2" style="color:var(--gold)"></i>
            Scholarship Recommendations
        </h2>
        <p>Scholarships matched to your eligibility based on academic results, income, study path, state, and other criteria</p>
    </div>

    @if(session('success'))
        <div class="alert-success mb-4" data-aos="fade-down">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
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
            <i class="fas fa-check-circle me-2"></i>
            All scholarships below meet your eligibility criteria.
            <strong>Priority Match</strong> scholarships align with your income group preference.
        </div>
    @endif

    @php
        $priorityGroup = $results->where('priority', 'priority_match');
        $generalGroup  = $results->where('priority', 'general_match');
    @endphp

    {{-- ── Priority Match group ────────────────────────────────────────── --}}
    @if($priorityGroup->isNotEmpty())

        <div class="group-divider" data-aos="fade-up">
            <span>
                <i class="fas fa-star me-1" style="color:var(--gold)"></i>
                Priority Match
            </span>
        </div>

        <div class="row">
            @foreach($priorityGroup as $scholarship)
                @php $breakdown = $scholarship->match_breakdown ?? []; @endphp

                <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                    <div class="recommendation-card">
                        <div class="recommendation-icon">🎓</div>
                        <div class="recommendation-content">

                            <h3 class="scholarship-title">{{ $scholarship->title }}</h3>
                            <div class="provider-name">
                                <i class="fas fa-building"></i>
                                {{ $scholarship->provider }}
                            </div>

                            <div class="recommendation-tags">
                                <span class="tag tag-eligible">
                                    <i class="fas fa-check-circle me-1"></i> Eligible
                                </span>
                                <span class="tag tag-priority">
                                    <i class="fas fa-star me-1"></i> Priority Match
                                </span>
                            </div>

                            <p class="scholarship-description">
                                {{ \Illuminate\Support\Str::limit(strip_tags($scholarship->description), 180) }}
                            </p>

                            <div class="breakdown-list">
                                @foreach($breakdown as $item)
                                    @php
                                        $passed    = $item['passed'] ?? false;
                                        $label     = $item['label']  ?? '';
                                        $reason    = $item['reason'] ?? '';
                                        $iconClass = $passed ? 'fa-check-circle pass' : 'fa-times-circle fail';
                                    @endphp
                                    <div class="breakdown-item">
                                        <i class="fas breakdown-icon {{ $iconClass }}"></i>
                                        <span class="breakdown-label">{{ $label }}</span>
                                        <span class="breakdown-detail">{{ $reason }}</span>
                                    </div>
                                @endforeach
                            </div>

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
            @endforeach
        </div>
    @endif

    {{-- ── General Match group ──────────────────────────────────────────── --}}
    @if($generalGroup->isNotEmpty())

        <div class="group-divider" data-aos="fade-up">
            <span>
                <i class="fas fa-info-circle me-1" style="color:#f59e0b"></i>
                General Match — outside preferred income group
            </span>
        </div>

        <div class="row">
            @foreach($generalGroup as $scholarship)
                @php $breakdown = $scholarship->match_breakdown ?? []; @endphp

                <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                    <div class="recommendation-card general-match">
                        <div class="recommendation-icon">🎓</div>
                        <div class="recommendation-content">

                            <h3 class="scholarship-title">{{ $scholarship->title }}</h3>
                            <div class="provider-name">
                                <i class="fas fa-building"></i>
                                {{ $scholarship->provider }}
                            </div>

                            <div class="recommendation-tags">
                                <span class="tag tag-eligible">
                                    <i class="fas fa-check-circle me-1"></i> Eligible
                                </span>
                                <span class="tag tag-general">
                                    <i class="fas fa-info-circle me-1"></i> General Match
                                </span>
                            </div>

                            <div class="general-notice">
                                <i class="fas fa-exclamation-circle"></i>
                                You are eligible for this scholarship but are outside its preferred income group.
                                You may still apply — priority (keutamaan) is given to other income categories.
                            </div>

                            <p class="scholarship-description">
                                {{ \Illuminate\Support\Str::limit(strip_tags($scholarship->description), 180) }}
                            </p>

                            <div class="breakdown-list">
                                @foreach($breakdown as $item)
                                    @php
                                        $passed    = $item['passed'] ?? false;
                                        $label     = $item['label']  ?? '';
                                        $reason    = $item['reason'] ?? '';
                                        $iconClass = $passed ? 'fa-check-circle pass' : 'fa-times-circle fail';
                                    @endphp
                                    <div class="breakdown-item">
                                        <i class="fas breakdown-icon {{ $iconClass }}"></i>
                                        <span class="breakdown-label">{{ $label }}</span>
                                        <span class="breakdown-detail">{{ $reason }}</span>
                                    </div>
                                @endforeach
                            </div>

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
            @endforeach
        </div>
    @endif

    {{-- ── Empty state ──────────────────────────────────────────────────── --}}
    @if($results->isEmpty())
        <div class="col-12">
            <div class="alert-info text-center py-5" data-aos="fade-up">
                <i class="fas fa-search fa-3x mb-3" style="display:block"></i>
                <h5 class="mb-2">No Scholarships Found</h5>
                <p class="mb-0">No scholarships match your current eligibility criteria. Try updating your profile or check back later.</p>
                <a href="{{ route('scholarship.finder') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-edit me-2"></i> Update Profile
                </a>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ duration: 800, once: true });</script>
@endpush
@endsection