@extends('layouts.app')

@section('title', 'Browse Scholarships')

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

    .browse-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        padding: 2rem;
        border-radius: 24px;
        margin-bottom: 2rem;
        border-left: 4px solid var(--gold);
    }

    .browse-header h2 {
        color: var(--maroon);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .browse-header p {
        color: var(--gray-600);
        margin-bottom: 0;
    }

    .search-box {
        background: white;
        border-radius: 60px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .search-box input {
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
    }

    .search-box input:focus {
        outline: none;
        box-shadow: none;
    }

    .search-box button {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        border: none;
        padding: 0.75rem 1.5rem;
        color: white;
        transition: all 0.3s ease;
    }

    .search-box button:hover {
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
    }

    .scholarship-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .scholarship-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }

    .scholarship-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--maroon));
    }

    .scholarship-card-body {
        padding: 1.5rem;
        flex: 1;
    }

    .scholarship-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--maroon);
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .scholarship-provider {
        color: var(--gray-600);
        font-size: 0.85rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .scholarship-provider i {
        color: var(--gold);
    }

    .scholarship-description {
        color: var(--gray-600);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .badge-custom {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-amount {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .badge-category {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }

    .deadline-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        margin-top: auto;
    }

    .deadline-normal {
        color: #065f46;
    }

    .deadline-soon {
        color: #92400e;
    }

    .deadline-expired {
        color: #991b1b;
    }

    .btn-view {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
        display: block;
    }

    .empty-state h4 {
        color: var(--gray-800);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gray-600);
        margin-bottom: 1.5rem;
    }

    .pagination {
        margin-top: 2rem;
    }

    .pagination .page-link {
        color: var(--maroon);
        border-radius: 8px;
        margin: 0 3px;
        border: 1px solid #e5e7eb;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        border-color: var(--maroon);
        color: white;
    }

    .pagination .page-link:hover {
        background: rgba(244, 197, 66, 0.2);
        color: var(--maroon);
        border-color: var(--gold);
    }

    @media (max-width: 768px) {
        .browse-header {
            padding: 1.5rem;
        }
        
        .browse-header h2 {
            font-size: 1.5rem;
        }
        
        .search-box input {
            font-size: 0.85rem;
            padding: 0.6rem 1rem;
        }
        
        .scholarship-title {
            font-size: 1rem;
        }
    }
</style>

<div class="container py-5">
    <div class="browse-header" data-aos="fade-up">
        <div class="row align-items-center">
            <div class="col-md-7 mb-3 mb-md-0">
                <h2>
                    <i class="fas fa-graduation-cap me-2" style="color: var(--gold);"></i>
                    Available Scholarships
                </h2>
                <p>Discover and apply for scholarships that match your qualifications</p>
            </div>
            <div class="col-md-5">
                <form action="{{ route('scholarships.search') }}" method="GET">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by title, provider, or field..." value="{{ request('search') }}">
                            <button class="btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($scholarships->count() > 0)
    <div class="row">
        @foreach($scholarships as $scholarship)
        @php
            $today = \Carbon\Carbon::today();
            $deadlineClass = '';
            $deadlineIcon = 'fa-calendar-alt';
            
            if($scholarship->deadline) {
                $deadline = \Carbon\Carbon::parse($scholarship->deadline);
                if($deadline->isPast()) {
                    $deadlineClass = 'deadline-expired';
                    $deadlineIcon = 'fa-clock';
                } elseif($deadline->diffInDays($today) <= 7) {
                    $deadlineClass = 'deadline-soon';
                    $deadlineIcon = 'fa-hourglass-half';
                } else {
                    $deadlineClass = 'deadline-normal';
                    $deadlineIcon = 'fa-calendar-check';
                }
            }
        @endphp
        <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
            <div class="scholarship-card">
                <div class="scholarship-card-body">
                    <h5 class="scholarship-title">{{ $scholarship->title }}</h5>
                    <div class="scholarship-provider">
                        <i class="fas fa-building"></i>
                        {{ $scholarship->provider }}
                    </div>
                    
                    <p class="scholarship-description">
                        {{ Str::limit($scholarship->description ?? 'No description available', 120) }}
                    </p>
                    
                    <div class="mb-3">
                        @if($scholarship->amount)
                            <span class="badge-custom badge-amount me-2">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                RM {{ number_format($scholarship->amount, 2) }}
                            </span>
                        @endif
                        
                        @if($scholarship->academic_category)
                            <span class="badge-custom badge-category">
                                <i class="fas fa-star me-1"></i>
                                {{ $scholarship->academic_category }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="deadline-info">
                        <i class="fas {{ $deadlineIcon }}"></i>
                        <span class="{{ $deadlineClass }}">
                            @if($scholarship->deadline)
                                Deadline: {{ \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y') }}
                            @else
                                Rolling / No Deadline
                            @endif
                        </span>
                    </div>
                </div>
                <div class="p-3 pt-0">
                    <a href="{{ route('scholarships.show', $scholarship->id) }}" class="btn-view">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $scholarships->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="empty-state" data-aos="fade-up">
        <i class="fas fa-search"></i>
        <h4>No Scholarships Found</h4>
        @if(request('search'))
            <p>No scholarships match your search criteria. Try different keywords or browse all scholarships.</p>
            <a href="{{ route('scholarships.browse') }}" class="btn-view">
                <i class="fas fa-eye"></i> View All Scholarships
            </a>
        @else
            <p>Check back later for new scholarship opportunities or adjust your filters.</p>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
@endpush
@endsection