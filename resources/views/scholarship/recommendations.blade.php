@extends('layouts.app')

@section('title', 'Scholarship Recommendations')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Scholarship Recommendations</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(!auth()->user()->profile)
        <div class="alert alert-warning">
            Please complete your profile to get personalized recommendations.
            <a href="{{ route('scholarship.finder') }}" class="btn btn-sm btn-primary ms-2">
                Complete Profile
            </a>
        </div>
    @else
        <div class="alert alert-success">
            Scholarships below are matched based on your academic results,
            income category, study path, and eligibility criteria.
        </div>
    @endif

    <div class="row">
        @forelse($results as $scholarship)

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">

                    <h5 class="card-title">
                        {{ $scholarship->title }}
                    </h5>

                    <p class="card-text text-muted">
                        {{ \Illuminate\Support\Str::limit(strip_tags($scholarship->description), 120) }}
                    </p>

                    {{-- ✅ ELIGIBLE LABEL --}}
                    <div class="mb-2">
                        <span class="badge bg-success">
                            Eligible
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">
                            Provider: {{ $scholarship->provider }}
                        </small><br>

                        <small class="text-muted">
                            Deadline:
                            {{ $scholarship->deadline
                                ? \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y')
                                : 'Not specified' }}
                        </small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        @if($scholarship->application_link)
                            <a href="{{ $scholarship->application_link }}"
                               target="_blank"
                               class="btn btn-primary btn-sm">
                                Apply Now
                            </a>
                        @endif

                        <a href="{{ route('scholarships.show', $scholarship->id) }}"
                           class="btn btn-outline-secondary btn-sm">
                            View Details
                        </a>

                        <form action="{{ route('bookmarks.toggle', $scholarship->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-bookmark"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        @empty
        <div class="col-12">
            <div class="alert alert-info">
                No scholarships found matching your criteria.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
