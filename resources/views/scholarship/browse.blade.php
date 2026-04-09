@extends('layouts.app')

@section('title', 'Browse Scholarships')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Available Scholarships</h2>
            <p class="text-muted">Find scholarships that match your profile</p>
        </div>
        <div class="col-md-4">
            <form action="{{ route('scholarships.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search scholarships...">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($scholarships->count() > 0)
    <div class="row">
        @foreach($scholarships as $scholarship)
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ $scholarship->title }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $scholarship->provider }}</h6>
                    
                    <p class="card-text">{{ Str::limit($scholarship->description, 150) }}</p>
                    
                    <div class="mb-3">
                        @if($scholarship->amount)
                        <span class="badge bg-success">RM {{ number_format($scholarship->amount, 2) }}</span>
                        @endif
                        
                        @if($scholarship->academic_category)
                        <span class="badge bg-info">{{ $scholarship->academic_category }}</span>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Deadline: {{ $scholarship->deadline->format('d M Y') }}
                        </small>
                        <a href="{{ route('scholarships.show', $scholarship->id) }}" class="btn btn-sm btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $scholarships->links() }}
    </div>
    @else
    <div class="text-center py-5">
        <div class="text-muted">
            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
            <h4>No scholarships available at the moment</h4>
            <p>Check back later or try adjusting your search criteria.</p>
        </div>
    </div>
    @endif
</div>
@endsection