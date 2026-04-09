@extends('layouts.admin')

@section('title', $scholarship->title)

@section('content')
@php
    $deadline = $scholarship->deadline
        ? \Carbon\Carbon::parse($scholarship->deadline)
        : null;
@endphp

<div class="container-fluid">
    <div class="row">
        <!-- MAIN CONTENT -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i> Scholarship Details
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('admin.scholarships.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="mb-3">{{ $scholarship->title }}</h4>

                    <!-- PROVIDER + DEADLINE -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong><i class="fas fa-building me-2 text-primary"></i> Provider:</strong>
                            <span class="ms-2">{{ $scholarship->provider }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar-times me-2 text-danger"></i> Deadline:</strong>
                            @if($deadline)
                                <span class="ms-2 {{ $deadline->isPast() ? 'text-danger' : 'text-success' }}">
                                    {{ $deadline->format('d M Y') }}
                                    ({{ $deadline->isPast() ? 'Expired' : $deadline->diffForHumans() }})
                                </span>
                            @else
                                <span class="ms-2 text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="mb-4">
                        <strong><i class="fas fa-align-left me-2 text-info"></i> Description:</strong>
                        <div class="card mt-2">
                            <div class="card-body bg-light">
                                {{ $scholarship->description }}
                            </div>
                        </div>
                    </div>

            

                    <!-- LINKS -->
                    @if($scholarship->application_link)
                        <div class="mb-3">
                            <strong><i class="fas fa-globe me-2 text-info"></i> Application Link:</strong><br>
                            <a href="{{ $scholarship->application_link }}" target="_blank">
                                {{ $scholarship->application_link }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-md-4">
           <!-- MATCHING CRITERIA (RULE-BASED) -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-filter me-2"></i> Eligibility Criteria 
        </h6>
    </div>

    <div class="card-body">
        @php
            $criteria = $scholarship->eligibilityCriteria;
        @endphp

        @if($criteria)

            {{-- Academic --}}
            <p>
                <strong>Academic:</strong>
                @if($criteria->min_spm_as)
                    ≥ {{ $criteria->min_spm_as }} A’s (SPM)
                @else
                    Any
                @endif
            </p>

            {{-- Income --}}
            <p>
                <strong>Income:</strong>
                @if($criteria->income_categories)
                    @php
                        $incomes = is_array($criteria->income_categories)
                            ? $criteria->income_categories
                            : json_decode($criteria->income_categories, true);
                    @endphp

                    {{ collect($incomes)->map(fn($i) =>
                        $i === 'B1' ? 'B40' : ($i === 'B3' ? 'M40' : $i)
                    )->implode(', ') }}
                @else
                    Any
                @endif
            </p>

            {{-- Study Path --}}
            <p>
                <strong>Study Path:</strong>
                @if($criteria->study_paths)
                    @php
                        $paths = is_array($criteria->study_paths)
                            ? $criteria->study_paths
                            : json_decode($criteria->study_paths, true);

                        $map = [
                            'C1' => 'Pre-University',
                            'C2' => 'Diploma',
                            'C3' => 'Degree',
                            'C4' => 'TVET',
                        ];
                    @endphp

                    {{ collect($paths)->map(fn($p) => $map[$p] ?? $p)->implode(', ') }}
                @else
                    Any
                @endif
            </p>

            {{-- Citizenship --}}
            @if($criteria->citizenship_required)
                <p>
                    <strong>Citizenship:</strong>
                    {{ $criteria->citizenship_required }}
                </p>
            @endif

            {{-- Age --}}
            @if($criteria->min_age || $criteria->max_age)
                <p>
                    <strong>Age:</strong>
                    {{ $criteria->min_age ?? 'Any' }} – {{ $criteria->max_age ?? 'Any' }}
                </p>
            @endif

            {{-- Bumiputera --}}
            @if($criteria->bumiputera_required)
                <p><strong>Bumiputera:</strong> Required</p>
            @elseif($criteria->bumiputera_priority)
                <p><strong>Bumiputera:</strong> Priority</p>
            @endif

            {{-- Bond --}}
            @if($criteria->bond_required)
                <p>
                    <strong>Service Bond:</strong>
                    Yes{{ $criteria->bond_years ? ' (' . $criteria->bond_years . ' years)' : '' }}
                </p>
            @endif

        @else
            <span class="text-muted">
                No eligibility rules defined
            </span>
        @endif
    </div>
</div>


            <!-- STATUS -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Status</h6>
                </div>
                <div class="card-body">
                    <span class="badge bg-{{ $scholarship->is_active ? 'success' : 'secondary' }}">
                        {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                    </span>

                    <div class="mt-3 text-muted small">
                        <strong>Created:</strong><br>
                        {{ $scholarship->created_at->format('d M Y, H:i') }}<br>
                        ({{ $scholarship->created_at->diffForHumans() }})
                    </div>

                    <div class="mt-3 text-muted small">
                        <strong>Last Updated:</strong><br>
                        {{ $scholarship->updated_at->format('d M Y, H:i') }}<br>
                        ({{ $scholarship->updated_at->diffForHumans() }})
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i> Quick Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    @if($scholarship->application_link)
                        <a href="{{ $scholarship->application_link }}" target="_blank" class="btn btn-info">
                            <i class="fas fa-external-link-alt me-2"></i> Open Application Page
                        </a>
                    @endif

                    <form method="POST" action="{{ route('admin.scholarships.toggle-status', $scholarship->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="btn btn-{{ $scholarship->is_active ? 'warning' : 'success' }}">
                            {{ $scholarship->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.scholarships.destroy', $scholarship->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this scholarship?')">
                            Delete Scholarship
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
