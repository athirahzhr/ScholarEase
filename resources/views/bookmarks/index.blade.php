@extends('layouts.app')

@section('title', 'My Bookmarks')

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

    .bookmarks-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        border-left: 4px solid var(--gold);
    }

    .bookmarks-header h2 {
        color: var(--maroon);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .bookmarks-header p {
        color: var(--gray-600);
        margin-bottom: 0;
    }

    .bookmark-card {
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

    .bookmark-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }

    .bookmark-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--maroon));
    }

    .bookmark-card-body {
        padding: 1.5rem;
        flex: 1;
    }

    .bookmark-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--maroon);
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }

    .bookmark-description {
        color: var(--gray-600);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .bookmark-details {
        background: #f9fafb;
        border-radius: 12px;
        padding: 0.75rem;
        margin-bottom: 1rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }

    .detail-item:last-child {
        margin-bottom: 0;
    }

    .detail-item i {
        width: 20px;
        color: var(--maroon);
    }

    .detail-item span {
        color: var(--gray-600);
    }

    .detail-item strong {
        color: var(--gray-800);
    }

    .deadline-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .deadline-soon {
        background: #fef3c7;
        color: #92400e;
    }

    .deadline-expired {
        background: #fee2e2;
        color: #991b1b;
    }

    .deadline-normal {
        background: #d1fae5;
        color: #065f46;
    }

    .btn-apply {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-apply:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        color: white;
    }

    .btn-remove {
        background: transparent;
        color: #dc2626;
        border: 1px solid #dc2626;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-remove:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
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

    .btn-find {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-find:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        color: white;
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-left: 4px solid #10b981;
        border-radius: 12px;
        color: #065f46;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .bookmarks-header {
            padding: 1.5rem;
        }
        
        .bookmark-title {
            font-size: 1.1rem;
        }
        
        .btn-apply, .btn-remove {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }
    }
</style>

<div class="container py-4">
    <div class="bookmarks-header" data-aos="fade-up">
        <h2>
            <i class="fas fa-bookmark me-2" style="color: var(--gold);"></i>
            My Bookmarked Scholarships
        </h2>
        <p>Scholarships you've saved for later review and application</p>
    </div>
    
    @if(session('success'))
        <div class="alert-success" data-aos="fade-down">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if($bookmarks->isEmpty())
        <div class="empty-state" data-aos="fade-up">
            <i class="fas fa-bookmark"></i>
            <h4>No Bookmarks Yet</h4>
            <p>You haven't bookmarked any scholarships yet. Start exploring and save the ones that interest you!</p>
            <a href="{{ route('scholarship.recommendations') }}" class="btn-find">
                <i class="fas fa-search"></i> Find Scholarships
            </a>
        </div>
    @else
        <div class="row">
            @foreach($bookmarks as $bookmark)
            @php 
                $scholarship = $bookmark->scholarship;
                $today = \Carbon\Carbon::today();
                $deadlineClass = '';
                $deadlineText = '';
                
                if($scholarship->deadline) {
                    $deadline = \Carbon\Carbon::parse($scholarship->deadline);
                    if($deadline->isPast()) {
                        $deadlineClass = 'deadline-expired';
                        $deadlineText = 'Expired';
                    } elseif($deadline->diffInDays($today) <= 7) {
                        $deadlineClass = 'deadline-soon';
                        $deadlineText = 'Closing Soon';
                    } else {
                        $deadlineClass = 'deadline-normal';
                        $deadlineText = 'Active';
                    }
                }
            @endphp
            <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                <div class="bookmark-card">
                    <div class="bookmark-card-body">
                        <h5 class="bookmark-title">{{ $scholarship->title ?? $scholarship->name }}</h5>
                        <p class="bookmark-description">{{ Str::limit($scholarship->description ?? 'No description available', 100) }}</p>
                        
                        <div class="bookmark-details">
                            <div class="detail-item">
                                <i class="fas fa-building"></i>
                                <span><strong>Provider:</strong> {{ $scholarship->provider }}</span>
                            </div>
                            
                            @if($scholarship->amount)
                            <div class="detail-item">
                                <i class="fas fa-money-bill-wave"></i>
                                <span><strong>Amount:</strong> RM {{ number_format($scholarship->amount, 2) }}</span>
                            </div>
                            @endif
                            
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span><strong>Deadline:</strong></span>
                                @if($scholarship->deadline)
                                    <span class="deadline-badge {{ $deadlineClass }}">
                                        <i class="fas {{ $deadline->isPast() ? 'fa-clock' : ($deadline->diffInDays($today) <= 7 ? 'fa-hourglass-half' : 'fa-calendar-check') }}"></i>
                                        {{ \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y') }}
                                        @if($deadlineText)
                                            <span class="ms-1">({{ $deadlineText }})</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="deadline-badge deadline-normal">
                                        <i class="fas fa-infinity"></i> Rolling / No Deadline
                                    </span>
                                @endif
                            </div>
                            
                            @if($bookmark->created_at)
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span><strong>Saved:</strong> {{ $bookmark->created_at->diffForHumans() }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            @if($scholarship->application_link)
                                <a href="{{ $scholarship->application_link }}" target="_blank" class="btn-apply">
                                    <i class="fas fa-external-link-alt"></i> Apply Now
                                </a>
                            @else
                                <button class="btn-apply" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="fas fa-info-circle"></i> No Link Available
                                </button>
                            @endif
                            
                            <form action="{{ route('bookmarks.destroy', $bookmark) }}" method="POST" onsubmit="return confirm('Remove this scholarship from your bookmarks?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-remove">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('scholarship.recommendations') }}" class="btn-find">
                <i class="fas fa-search"></i> Find More Scholarships
            </a>
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