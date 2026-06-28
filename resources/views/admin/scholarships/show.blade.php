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

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold text-maroon">
                <i class="fas fa-graduation-cap me-2"></i>
                Scholarship Details
            </h4>
            <p class="text-muted mb-0 mt-1">View complete information about this scholarship</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Edit Scholarship
            </a>
            <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <div class="row">
        {{-- ================= MAIN CONTENT ================= --}}
        <div class="col-lg-8">

            {{-- SCHOLARSHIP OVERVIEW --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Scholarship Overview
                    </h5>
                </div>
                <div class="card-body">
                    {{-- TITLE --}}
                    <h4 class="mb-3 fw-bold" style="color: #7A0019;">
                        {{ $scholarship->title }}
                    </h4>

                    {{-- PROVIDER & DEADLINE --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="info-box p-3 bg-maroon-light rounded-3">
                                <label class="text-muted small text-uppercase fw-semibold d-block mb-1">
                                    <i class="fas fa-building me-1"></i> Provider
                                </label>
                                <span class="fw-semibold fs-6">{{ $scholarship->provider }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box p-3 bg-gold-light rounded-3">
                                <label class="text-muted small text-uppercase fw-semibold d-block mb-1">
                                    <i class="fas fa-calendar-alt me-1"></i> Deadline
                                </label>
                                @if($deadline)
                                    <span class="fw-semibold fs-6 {{ $deadline->isPast() ? 'text-danger' : 'text-success' }}">
                                        {{ $deadline->format('d M Y') }}
                                        <span class="small fw-normal">
                                            ({{ $deadline->isPast() ? 'Expired' : $deadline->diffForHumans() }})
                                        </span>
                                    </span>
                                @else
                                    <span class="text-muted">Rolling / No Deadline</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-maroon mb-2">
                            <i class="fas fa-align-left me-2"></i>
                            Description
                        </h6>
                        <div class="description-box p-4" style="background: linear-gradient(135deg, #FFF8EE, #f5ebe0); border-radius: 16px; border-left: 4px solid #F4C542;">
                            {{ $scholarship->description }}
                        </div>
                    </div>

                    {{-- RAW ELIGIBILITY --}}
                    @if($scholarship->raw_eligibility)
                        <div class="mb-4">
                            <h6 class="fw-bold text-maroon mb-2">
                                <i class="fas fa-list-check me-2"></i>
                                Raw Eligibility (Source)
                            </h6>
                            <div class="raw-eligibility-box p-4" style="background: #f9fafb; border-radius: 16px; border: 1px solid #e5e7eb;">
                                {!! nl2br(e($scholarship->raw_eligibility)) !!}
                            </div>
                        </div>
                    @endif

                    {{-- APPLICATION LINK --}}
                    @if($scholarship->application_link)
                        <div>
                            <h6 class="fw-bold text-maroon mb-2">
                                <i class="fas fa-globe me-2"></i>
                                Application Link
                            </h6>
                            <a href="{{ $scholarship->application_link }}" target="_blank" class="application-link d-inline-flex align-items-center p-3 rounded-3" style="background: #f0f7ff; border: 1px solid #dbeafe; text-decoration: none; transition: all 0.3s ease;">
                                <i class="fas fa-external-link-alt me-2" style="color: #7A0019;"></i>
                                <span style="color: #1e40af; font-weight: 500;">{{ Str::limit($scholarship->application_link, 60) }}</span>
                                <i class="fas fa-arrow-right ms-2" style="color: #7A0019; font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ELIGIBILITY CRITERIA (Full View) --}}
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Eligibility Criteria
                    </h5>
                </div>
                <div class="card-body">
                    @if($criteria)
                        <div class="row g-4">
                            {{-- Academic --}}
                            @if($criteria->min_spm_as || $criteria->max_spm_as)
                                <div class="col-md-6">
                                    <div class="criteria-item p-3 bg-maroon-light rounded-3 h-100">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="fas fa-star me-1" style="color: #F4C542;"></i> Academic
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @if($criteria->min_spm_as)
                                                <span class="badge-custom badge-maroon">
                                                    <i class="fas fa-arrow-up me-1"></i> Min {{ $criteria->min_spm_as }} A's
                                                </span>
                                            @endif
                                            @if($criteria->max_spm_as)
                                                <span class="badge-custom badge-dark">
                                                    <i class="fas fa-arrow-down me-1"></i> Max {{ $criteria->max_spm_as }} A's
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Income --}}
                            @if($criteria->max_monthly_income || !empty($criteria->income_categories))
                                <div class="col-md-6">
                                    <div class="criteria-item p-3 bg-success-light rounded-3 h-100">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="fas fa-money-bill-wave me-1" style="color: #10b981;"></i> Financial
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @if($criteria->max_monthly_income)
                                                <span class="badge-custom badge-success">
                                                    <i class="fas fa-ring me-1"></i> RM {{ number_format($criteria->max_monthly_income, 2) }}
                                                </span>
                                            @endif
                                            @if(!empty($criteria->income_categories))
                                                @foreach($criteria->income_categories as $category)
                                                    <span class="badge-custom badge-success">{{ $category }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Study Level --}}
                            @if(!empty($criteria->study_paths))
                                <div class="col-md-6">
                                    <div class="criteria-item p-3 bg-gold-light rounded-3 h-100">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="fas fa-graduation-cap me-1" style="color: #92400e;"></i> Study Levels
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach($criteria->study_paths as $path)
                                                <span class="badge-custom badge-gold">{{ $path }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Field of Study --}}
                            @if(!empty($criteria->fields_of_study))
                                <div class="col-md-6">
                                    <div class="criteria-item p-3 bg-info-light rounded-3 h-100">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="fas fa-book me-1" style="color: #1e40af;"></i> Fields of Study
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach(array_slice($criteria->fields_of_study, 0, 4) as $field)
                                                <span class="badge-custom badge-info">{{ Str::limit($field, 20) }}</span>
                                            @endforeach
                                            @if(count($criteria->fields_of_study) > 4)
                                                <span class="badge-custom badge-secondary">+{{ count($criteria->fields_of_study) - 4 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Citizenship & State --}}
                            @if($criteria->citizenship_required || $criteria->state_requirement)
                                <div class="col-md-6">
                                    <div class="criteria-item p-3 bg-dark-light rounded-3 h-100">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="fas fa-passport me-1" style="color: #374151;"></i> Location
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @if($criteria->citizenship_required)
                                                <span class="badge-custom badge-dark">
                                                    <i class="fas fa-flag me-1"></i> {{ $criteria->citizenship_required }}
                                                </span>
                                            @endif
                                            @if($criteria->state_requirement)
                                                <span class="badge-custom badge-maroon">
                                                    <i class="fas fa-map-marker-alt me-1"></i> {{ $criteria->state_requirement }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Age --}}
                            @if($criteria->min_age || $criteria->max_age)
                                <div class="col-md-6">
                                    <div class="criteria-item p-3 bg-warning-light rounded-3 h-100">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="fas fa-birthday-cake me-1" style="color: #92400e;"></i> Age Requirement
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <span class="badge-custom badge-gold">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $criteria->min_age ?? 'Any' }} - {{ $criteria->max_age ?? 'Any' }} years
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Bumiputera --}}
                            @if($criteria->bumiputera_required)
                                <div class="col-md-6">
                                    <div class="criteria-item p-3 bg-danger-light rounded-3 h-100">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="fas fa-star-of-life me-1" style="color: #dc2626;"></i> Special Requirement
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <span class="badge-custom badge-danger">
                                                <i class="fas fa-check-circle me-1"></i> Bumiputera Required
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list" style="font-size: 56px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                            <h6 class="text-muted">No eligibility rules defined for this scholarship.</h6>
                            <p class="text-muted small">Add eligibility criteria by editing this scholarship.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="col-lg-4">

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
                        {{-- Status --}}
                        <div class="status-row d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-semibold text-muted">Current Status</span>
                            <span class="status-badge {{ $scholarship->is_active ? 'status-active' : 'status-inactive' }}">
                                <i class="fas {{ $scholarship->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        {{-- Official --}}
                        <div class="status-row d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-semibold text-muted">Scholarship Type</span>
                            <span class="badge-custom {{ $scholarship->is_official ? 'badge-success' : 'badge-secondary' }}">
                                {{ $scholarship->is_official ? 'Official' : 'Third Party' }}
                            </span>
                        </div>

                        {{-- Created --}}
                        <div class="status-row d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-semibold text-muted">Created</span>
                            <span class="small">
                                {{ $scholarship->created_at->format('d M Y') }}
                                <span class="text-muted">({{ $scholarship->created_at->diffForHumans() }})</span>
                            </span>
                        </div>

                        {{-- Updated --}}
                        <div class="status-row d-flex justify-content-between align-items-center py-2">
                            <span class="fw-semibold text-muted">Last Updated</span>
                            <span class="small">
                                {{ $scholarship->updated_at->format('d M Y') }}
                                <span class="text-muted">({{ $scholarship->updated_at->diffForHumans() }})</span>
                            </span>
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
                    <div class="d-grid gap-3">
                        {{-- VIEW APPLICATION PAGE --}}
                        @if($scholarship->application_link)
                            <a href="{{ $scholarship->application_link }}" target="_blank" class="btn btn-action-primary">
                                <i class="fas fa-external-link-alt me-2"></i>
                                View Application Page
                            </a>
                        @endif

                        {{-- TOGGLE STATUS --}}
                        <form method="POST" action="{{ route('admin.scholarships.toggle-status', $scholarship->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-action-toggle w-100">
                                <i class="fas {{ $scholarship->is_active ? 'fa-eye-slash' : 'fa-eye' }} me-2"></i>
                                {{ $scholarship->is_active ? 'Deactivate' : 'Activate' }} Scholarship
                            </button>
                        </form>

                        {{-- EDIT --}}
                        <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" class="btn btn-action-edit">
                            <i class="fas fa-edit me-2"></i>
                            Edit Scholarship
                        </a>

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
    /* ============================================ */
    /* CARD STYLES                                 */
    /* ============================================ */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        background: rgba(122, 0, 25, 0.04);
        border-bottom: 2px solid rgba(244, 197, 66, 0.3);
        padding: 14px 20px;
    }

    .card-header h5,
    .card-header h6 {
        color: #7A0019;
        font-weight: 700;
        margin: 0;
    }

    .card-body {
        padding: 20px;
    }

    /* ============================================ */
    /* PAGE HEADER                                 */
    /* ============================================ */
    .text-maroon {
        color: #7A0019;
    }

    /* ============================================ */
    /* BUTTON STYLES                               */
    /* ============================================ */
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.25);
        background: linear-gradient(115deg, #4e0010, #7A0019);
        color: white;
    }

    .btn-outline-secondary {
        border: 2px solid #d1d5db;
        color: #6b7280;
        background: transparent;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-outline-secondary:hover {
        background: #6b7280;
        color: white;
        border-color: #6b7280;
        transform: translateY(-2px);
    }

    /* ============================================ */
    /* BADGE CUSTOM                                */
    /* ============================================ */
    .badge-custom {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        gap: 4px;
    }

    .badge-maroon {
        background: rgba(122, 0, 25, 0.12);
        color: #7A0019;
    }

    .badge-gold {
        background: rgba(244, 197, 66, 0.15);
        color: #92400e;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-dark {
        background: #e5e7eb;
        color: #374151;
    }

    .badge-secondary {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* ============================================ */
    /* BACKGROUND UTILITY                          */
    /* ============================================ */
    .bg-maroon-light {
        background: rgba(122, 0, 25, 0.05);
    }

    .bg-gold-light {
        background: rgba(244, 197, 66, 0.08);
    }

    .bg-success-light {
        background: rgba(16, 185, 129, 0.06);
    }

    .bg-info-light {
        background: rgba(30, 64, 175, 0.06);
    }

    .bg-dark-light {
        background: rgba(55, 65, 81, 0.04);
    }

    .bg-warning-light {
        background: rgba(244, 197, 66, 0.08);
    }

    .bg-danger-light {
        background: rgba(220, 38, 38, 0.06);
    }

    /* ============================================ */
    /* STATUS BADGE                                */
    /* ============================================ */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 14px;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ============================================ */
    /* ACTION BUTTONS                              */
    /* ============================================ */
    .btn-action-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.65rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.25);
        color: white;
    }

    .btn-action-toggle {
        background: linear-gradient(115deg, #F4C542, #e6b13e);
        color: #2c1a00;
        border: none;
        border-radius: 40px;
        padding: 0.65rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-action-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(244, 197, 66, 0.35);
    }

    .btn-action-edit {
        background: #6b7280;
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.65rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-action-edit:hover {
        background: #4b5563;
        transform: translateY(-2px);
        color: white;
    }

    .btn-action-delete {
        background: linear-gradient(115deg, #dc2626, #b91c1c);
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.65rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-action-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        color: white;
    }

    /* ============================================ */
    /* APPLICATION LINK                            */
    /* ============================================ */
    .application-link {
        transition: all 0.3s ease;
    }

    .application-link:hover {
        background: #dbeafe !important;
        border-color: #93c5fd !important;
        transform: translateX(4px);
    }

    /* ============================================ */
    /* CRITERIA ITEM                               */
    /* ============================================ */
    .criteria-item {
        transition: all 0.3s ease;
    }

    .criteria-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    /* ============================================ */
    /* RESPONSIVE                                  */
    /* ============================================ */
    @media (max-width: 992px) {
        .d-flex.align-items-center.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .d-flex.align-items-center.justify-content-between .d-flex {
            width: 100%;
        }

        .d-flex.align-items-center.justify-content-between .btn {
            flex: 1;
        }
    }

    @media (max-width: 768px) {
        .card-header {
            padding: 12px 16px;
        }

        .card-body {
            padding: 16px;
        }

        .row.g-4 {
            --bs-gutter-y: 0.75rem;
        }

        .criteria-item {
            padding: 12px !important;
        }

        .description-box,
        .raw-eligibility-box {
            padding: 12px !important;
        }

        .btn-primary,
        .btn-outline-secondary {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            width: 100%;
        }

        .d-flex.gap-2 {
            gap: 0.5rem !important;
        }

        .info-box {
            padding: 12px !important;
        }

        .text-maroon {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding: 0 8px !important;
        }

        .card-body {
            padding: 12px;
        }

        .badge-custom {
            font-size: 0.7rem;
            padding: 3px 10px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 3px 10px;
        }

        .btn-action-primary,
        .btn-action-toggle,
        .btn-action-edit,
        .btn-action-delete {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
    }
</style>
@endpush

@endsection