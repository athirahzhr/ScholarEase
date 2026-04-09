@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Uncategorized Scholarships</h2>
        <div>
            <a href="{{ route('admin.scholarships.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to All Scholarships
            </a>
            <form action="{{ route('admin.quick-recategorize') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Re-categorize all scholarships?')">
                    <i class="fas fa-tags me-1"></i> Auto-Categorize All
                </button>
            </form>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Scholarships</h6>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Fully Categorized</h6>
                    <h2 class="mb-0">{{ $stats['fully_categorized'] }}</h2>
                    <small>{{ $stats['percentage'] }}% Complete</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Need Categorization</h6>
                    <h2 class="mb-0">{{ $stats['partially_categorized'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Uncategorized Scholarships Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Scholarships Needing Categorization</h5>
        </div>
        <div class="card-body">
            @if($uncategorized->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Provider</th>
                            <th>Academic</th>
                            <th>Income</th>
                            <th>Study Path</th>
                            <th>Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uncategorized as $scholarship)
                        <tr>
                            <td>
                                <strong>{{ Str::limit($scholarship->title, 40) }}</strong>
                                <div class="small text-muted">
                                    {{ Str::limit($scholarship->description, 50) }}
                                </div>
                            </td>
                            <td>{{ $scholarship->provider }}</td>
                            <td>
                                @if($scholarship->academic_category)
                                <span class="badge bg-success">{{ $scholarship->academic_category }}</span>
                                @else
                                <span class="badge bg-danger">Missing</span>
                                @endif
                            </td>
                            <td>
                                @if($scholarship->income_category)
                                <span class="badge bg-success">{{ $scholarship->income_category }}</span>
                                @else
                                <span class="badge bg-danger">Missing</span>
                                @endif
                            </td>
                            <td>
                                @if($scholarship->study_path)
                                <span class="badge bg-success">{{ $scholarship->study_path }}</span>
                                @else
                                <span class="badge bg-danger">Missing</span>
                                @endif
                            </td>
                            <td>{{ $scholarship->deadline->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.scholarships.edit', $scholarship) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.scholarships.show', $scholarship) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <p class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    To categorize: Edit each scholarship and fill in Academic, Income, and Study Path categories.
                </p>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h4>All scholarships are categorized!</h4>
                <p class="text-muted">Great job! All scholarships have complete categorization data.</p>
                <a href="{{ route('admin.scholarships.index') }}" class="btn btn-primary">
                    <i class="fas fa-graduation-cap me-1"></i> View All Scholarships
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection