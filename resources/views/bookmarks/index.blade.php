@extends('layouts.app')

@section('title', 'My Bookmarks')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">My Bookmarked Scholarships</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if($bookmarks->isEmpty())
        <div class="alert alert-info">
            You haven't bookmarked any scholarships yet.
            <a href="{{ route('scholarship.recommendations') }}" class="btn btn-sm btn-primary ms-2">Find Scholarships</a>
        </div>
    @else
        <div class="row">
            @foreach($bookmarks as $bookmark)
            @php $scholarship = $bookmark->scholarship @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $scholarship->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($scholarship->description, 100) }}</p>
                        
                        <div class="mb-3">
                            <small class="text-muted">Provider: {{ $scholarship->provider }}</small><br>
                            @if($scholarship->amount)
                                <small class="text-muted">Amount: RM {{ number_format($scholarship->amount, 2) }}</small><br>
                            @endif
                            <small class="text-muted">
                            Deadline:
                            {{ $scholarship->deadline
                                ? \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y')
                                : 'Not specified' }}
                        </small>

                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ $scholarship->application_link }}" target="_blank" class="btn btn-primary btn-sm">
                                Apply Now
                            </a>
                            
                            <form action="{{ route('bookmarks.destroy', $bookmark) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection