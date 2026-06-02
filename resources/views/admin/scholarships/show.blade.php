@extends('layouts.admin')

@section('title', $scholarship->title)

@section('content')

@php
    $deadline = $scholarship->deadline
        ? \Carbon\Carbon::parse($scholarship->deadline)
        : null;

    $criteria = $scholarship->eligibilityCriteria;
@endphp

<div class="container-fluid px-0">

    <div class="row">

        {{-- ================= MAIN CONTENT ================= --}}
        <div class="col-md-8">

            <div class="card shadow-sm border-0 mb-4">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Scholarship Details
                    </h5>

                    <div class="btn-group">
                        <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" class="btn btn-sm btn-edit">
                            <i class="fas fa-edit me-1"></i>
                            Edit
                        </a>
                        <a href="{{ route('admin.scholarships.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back
                        </a>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="card-body">
                    {{-- TITLE --}}
                    <h4 class="mb-4" style="color: #7A0019; font-weight: 700;">
                        {{ $scholarship->title }}
                    </h4>

                    {{-- PROVIDER + DEADLINE --}}
                    <div class="row mb-4">
                        {{-- PROVIDER --}}
                        <div class="col-md-6">
                            <div class="info-box">
                                <strong><i class="fas fa-building me-2" style="color: #7A0019;"></i> Provider:</strong>
                                <span class="ms-2">{{ $scholarship->provider }}</span>
                            </div>
                        </div>

                        {{-- DEADLINE --}}
                        <div class="col-md-6">
                            <div class="info-box">
                                <strong><i class="fas fa-calendar-alt me-2" style="color: #7A0019;"></i> Deadline:</strong>
                                @if($deadline)
                                    <span class="ms-2 {{ $deadline->isPast() ? 'text-danger' : 'text-success' }} fw-semibold">
                                        {{ $deadline->format('d M Y') }}
                                        <span class="small">
                                            ({{ $deadline->isPast() ? 'Expired' : $deadline->diffForHumans() }})
                                        </span>
                                    </span>
                                @else
                                    <span class="ms-2 text-muted">Rolling / No Deadline</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mb-4">
                        <strong><i class="fas fa-align-left me-2" style="color: #7A0019;"></i> Description:</strong>
                        <div class="description-box mt-2">
                            <div class="p-3" style="background: linear-gradient(135deg, #FFF8EE, #f5ebe0); border-radius: 16px; border-left: 4px solid #F4C542;">
                                {{ $scholarship->description }}
                            </div>
                        </div>
                    </div>

                    {{-- RAW ELIGIBILITY --}}
                    @if($scholarship->raw_eligibility)
                        <div class="mb-4">
                            <strong><i class="fas fa-list-check me-2" style="color: #7A0019;"></i> Raw Eligibility:</strong>
                            <div class="raw-eligibility-box mt-2">
                                <div class="p-3" style="background: #f9fafb; border-radius: 16px; border: 1px solid #e5e7eb;">
                                    {!! nl2br(e($scholarship->raw_eligibility)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- APPLICATION LINK --}}
                    @if($scholarship->application_link)
                        <div class="mb-3">
                            <strong><i class="fas fa-globe me-2" style="color: #7A0019;"></i> Application Link:</strong>
                            <div class="mt-2">
                                <a href="{{ $scholarship->application_link }}" target="_blank" class="application-link">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    {{ Str::limit($scholarship->application_link, 60) }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="col-md-4">
            {{-- ELIGIBILITY CRITERIA --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Eligibility Criteria
                    </h6>
                </div>
                <div class="card-body">
                    @if($criteria)
                        <div class="eligibility-list">
                            {{-- ACADEMIC --}}
                            @if($criteria->min_spm_as || $criteria->max_spm_as)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-star me-2" style="color: #F4C542;"></i> Academic:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        @if($criteria->min_spm_as)
                                            <span class="badge badge-maroon">
                                                <i class="fas fa-arrow-up me-1"></i> Min {{ $criteria->min_spm_as }} A's
                                            </span>
                                        @endif
                                        @if($criteria->max_spm_as)
                                            <span class="badge badge-dark">
                                                <i class="fas fa-arrow-down me-1"></i> Max {{ $criteria->max_spm_as }} A's
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- INCOME --}}
                            @if($criteria->max_monthly_income)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-money-bill-wave me-2" style="color: #10b981;"></i> Maximum Monthly Income:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-success">
                                            <i class="fas fa-ring me-1"></i> RM {{ number_format($criteria->max_monthly_income, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- STUDY LEVEL --}}
                            @if(!empty($criteria->study_paths))
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-graduation-cap me-2" style="color: #92400e;"></i> Study Levels:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        @foreach($criteria->study_paths as $path)
                                            <span class="badge badge-gold">{{ $path }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- FIELD OF STUDY --}}
                            @if(!empty($criteria->fields_of_study))
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-book me-2" style="color: #1e40af;"></i> Fields of Study:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        @foreach(array_slice($criteria->fields_of_study, 0, 6) as $field)
                                            <span class="badge badge-info">{{ Str::limit($field, 25) }}</span>
                                        @endforeach
                                        @if(count($criteria->fields_of_study) > 6)
                                            <span class="badge badge-secondary">+{{ count($criteria->fields_of_study) - 6 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- GENDER --}}
                            @if($criteria->gender_requirement && $criteria->gender_requirement !== 'Any')
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-venus-mars me-2" style="color: #6b7280;"></i> Gender Requirement:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-secondary">{{ $criteria->gender_requirement }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- CITIZENSHIP --}}
                            @if($criteria->citizenship_required)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-passport me-2" style="color: #374151;"></i> Citizenship:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-dark">{{ $criteria->citizenship_required }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- STATE --}}
                            @if($criteria->state_requirement)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-map-marker-alt me-2" style="color: #7A0019;"></i> State Requirement:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-maroon">{{ $criteria->state_requirement }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- AGE --}}
                            @if($criteria->min_age || $criteria->max_age)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-birthday-cake me-2" style="color: #92400e;"></i> Age Requirement:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-gold">
                                            {{ $criteria->min_age ?? 'Any' }} - {{ $criteria->max_age ?? 'Any' }} years
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- BUMIPUTERA --}}
                            @if($criteria->bumiputera_required)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-star-of-life me-2" style="color: #dc2626;"></i> Bumiputera:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-danger">Required</span>
                                    </div>
                                </div>
                            @elseif($criteria->bumiputera_priority)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-star-of-life me-2" style="color: #6b7280;"></i> Bumiputera:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-secondary">Priority Given</span>
                                    </div>
                                </div>
                            @endif

                            {{-- LEADERSHIP --}}
                            @if($criteria->leadership_required)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-trophy me-2" style="color: #dc2626;"></i> Leadership:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-danger">Required</span>
                                    </div>
                                </div>
                            @elseif($criteria->leadership_priority)
                                <div class="eligibility-item">
                                    <div class="eligibility-label">
                                        <i class="fas fa-trophy me-2" style="color: #6b7280;"></i> Leadership:
                                    </div>
                                    <div class="eligibility-badges mt-2">
                                        <span class="badge badge-secondary">Priority Given</span>
                                    </div>
                                </div>
                            @endif

                            {{-- PRIORITY WEIGHT --}}
                            <div class="eligibility-item">
                                <div class="eligibility-label">
                                    <i class="fas fa-chart-line me-2" style="color: #F4C542;"></i> Priority Weight:
                                </div>
                                <div class="eligibility-badges mt-2">
                                    <span class="badge badge-gold fw-bold">{{ $criteria->priority_weight ?? 1 }} / 10</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                            <p class="text-muted">No eligibility rules defined for this scholarship.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- STATUS CARD --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Status Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="status-info">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Current Status:</span>
                            <span class="status-badge {{ $scholarship->is_active ? 'status-active' : 'status-inactive' }}">
                                <i class="fas {{ $scholarship->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calendar-plus me-2" style="color: #7A0019;"></i> Created:
                            </div>
                            <div class="info-value">
                                {{ $scholarship->created_at->format('d M Y, H:i') }}
                                <span class="text-muted small">({{ $scholarship->created_at->diffForHumans() }})</span>
                            </div>
                        </div>

                        <div class="info-row mt-3">
                            <div class="info-label">
                                <i class="fas fa-calendar-edit me-2" style="color: #7A0019;"></i> Last Updated:
                            </div>
                            <div class="info-value">
                                {{ $scholarship->updated_at->format('d M Y, H:i') }}
                                <span class="text-muted small">({{ $scholarship->updated_at->diffForHumans() }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- APPLICATION PAGE --}}
                        @if($scholarship->application_link)
                            <a href="{{ $scholarship->application_link }}" target="_blank" class="btn btn-action-primary">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Open Application Page
                            </a>
                        @endif

                        {{-- TOGGLE STATUS --}}
                        <form method="POST" action="{{ route('admin.scholarships.toggle-status', $scholarship->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-action-toggle w-100">
                                <i class="fas {{ $scholarship->is_active ? 'fa-eye-slash' : 'fa-eye' }} me-2"></i>
                                {{ $scholarship->is_active ? 'Deactivate Scholarship' : 'Activate Scholarship' }}
                            </button>
                        </form>

                        {{-- DELETE --}}
                        <form method="POST" action="{{ route('admin.scholarships.destroy', $scholarship->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action-delete w-100" onclick="return confirm('⚠️ Are you sure you want to delete this scholarship?\n\nThis action cannot be undone!')">
                                <i class="fas fa-trash-alt me-2"></i>
                                Delete Permanently
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-bottom: 2px solid #F4C542;
        padding: 15px 20px;
        color: #7A0019;
        font-weight: 700;
    }
    
    .card-header h5, .card-header h6 {
        color: #7A0019;
        font-weight: 700;
    }
    
    .btn-edit {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.4rem 1rem;
        transition: all 0.3s ease;
    }
    
    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        color: white;
    }
    
    .btn-secondary {
        background: #6b7280;
        border: none;
        border-radius: 40px;
        padding: 0.4rem 1rem;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }
    
    .application-link {
        color: #7A0019;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .application-link:hover {
        color: #F4C542;
        text-decoration: underline;
    }
    
    /* Badge Styles */
    .badge-maroon {
        background: linear-gradient(135deg, rgba(122,0,25,0.15), rgba(122,0,25,0.08));
        color: #7A0019;
        border: 1px solid rgba(122,0,25,0.2);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-gold {
        background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.08));
        color: #92400e;
        border: 1px solid rgba(244,197,66,0.3);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-dark {
        background: linear-gradient(135deg, #e5e7eb, #d1d5db);
        color: #374151;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-secondary {
        background: #f3f4f6;
        color: #6b7280;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .status-active {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    
    .status-inactive {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }
    
    /* Eligibility List */
    .eligibility-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .eligibility-item {
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .eligibility-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .eligibility-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .eligibility-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    /* Info Rows */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.5rem 0;
    }
    
    .info-label {
        font-weight: 600;
        color: #7A0019;
    }
    
    .info-value {
        color: #4b5563;
        text-align: right;
    }
    
    /* Action Buttons */
    .btn-action-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.6rem 1rem;
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
    }
    
    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        color: white;
    }
    
    .btn-action-toggle {
        background: linear-gradient(115deg, #F4C542, #e6b13e);
        color: #2c1a00;
        border: none;
        border-radius: 40px;
        padding: 0.6rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-action-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(244, 197, 66, 0.4);
    }
    
    .btn-action-delete {
        background: linear-gradient(115deg, #dc2626, #b91c1c);
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.6rem 1rem;
        transition: all 0.3s ease;
    }
    
    .btn-action-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
    }
    
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .info-row {
            flex-direction: column;
        }
        
        .info-value {
            text-align: left;
            margin-top: 0.25rem;
        }
    }
</style>
@endpush

@endsection