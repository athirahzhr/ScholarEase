@extends('layouts.admin')

@section('title', 'Scraping Management')

@section('content')
<div class="container-fluid px-0">

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Scholarships</p>
                        <h2 class="mb-0 fw-bold" style="color: #7A0019;">{{ $totalScholarships }}</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Active Scholarships</p>
                        <h2 class="mb-0 fw-bold" style="color: #10b981;">{{ $activeScholarships }}</h2>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Today's Scrapes</p>
                        <h2 class="mb-0 fw-bold" style="color: #F4C542;">{{ $todayScrapes ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.05));">
                        <i class="fas fa-calendar-day" style="color: #F4C542;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Scrapes</p>
                        <h2 class="mb-0 fw-bold" style="color: #7A0019;">{{ $totalScrapes ?? $logs->total() }}</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scraping Logs Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-history me-2"></i> Scraping Logs
            </div>
            <div>
                <span class="badge" style="background: #7A0019; color: white;">
                    <i class="fas fa-database me-1"></i> Total Records: {{ $logs->total() }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i> #</th>
                            <th><i class="fas fa-globe me-1"></i> Website</th>
                            <th><i class="fas fa-chart-line me-1"></i> Status</th>
                            <th><i class="fas fa-plus-circle me-1"></i> Scholarships Added</th>
                            <th><i class="fas fa-stopwatch me-1"></i> Duration (s)</th>
                            <th><i class="fas fa-calendar-alt me-1"></i> Executed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="badge" style="background: #f3f4f6; color: #4b5563;">
                                    {{ $loop->iteration }}
                                </span>
                            </td>
                            
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="website-icon me-2">
                                        @php
                                            $icon = match(strtolower($log->source_website)) {
                                                'jpa' => 'fa-building',
                                                'mara' => 'fa-landmark',
                                                default => 'fa-globe'
                                            };
                                        @endphp
                                        <i class="fas {{ $icon }}" style="color: #7A0019;"></i>
                                    </div>
                                    <div>
                                        <strong class="text-uppercase">{{ $log->source_website }}</strong>
                                        @if($log->source_website == 'JPA')
                                            <br><small class="text-muted">Jabatan Perkhidmatan Awam</small>
                                        @elseif($log->source_website == 'MARA')
                                            <br><small class="text-muted">Majlis Amanah Rakyat</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td>
                                @if($log->status === 'success')
                                    <span class="status-badge status-success">
                                        <i class="fas fa-check-circle me-1"></i> Success
                                    </span>
                                @else
                                    <span class="status-badge status-failed">
                                        <i class="fas fa-exclamation-circle me-1"></i> Failed
                                    </span>
                                @endif
                            </td>
                            
                            <td>
                                <span class="badge" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; padding: 6px 12px;">
                                    <i class="fas fa-plus-circle me-1"></i> +{{ $log->inserted_count }}
                                </span>
                            </td>
                            
                            <td>
                                @if($log->started_at && $log->finished_at)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-stopwatch me-1" style="color: #F4C542;"></i>
                                        <span class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($log->started_at)->diffInSeconds(\Carbon\Carbon::parse($log->finished_at)) }} s
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($log->started_at)->format('d M Y') }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($log->started_at)->format('H:i:s') }}
                                    </small>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-robot" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                <h5 class="text-muted">No Scraping Logs Found</h5>
                                <p class="text-muted">Run your first scraper to see logs here.</p>
                                <a href="{{ route('admin.scraper.index') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-play-circle me-2"></i> Run Scraper
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($logs->hasPages())
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: white;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #F4C542, #7A0019);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(244,197,66,0.1));
        color: #7A0019;
    }
    
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
        font-weight: 700;
        color: #7A0019;
        font-size: 1.1rem;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        color: #7A0019;
        font-weight: 700;
        border-bottom: 2px solid #F4C542;
        padding: 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(244, 197, 66, 0.08);
        cursor: pointer;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    
    .status-failed {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }
    
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 60px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, #4e0010, #7A0019);
    }
    
    .website-icon {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(244,197,66,0.1));
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 15px 20px;
    }
    
    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        color: #7A0019;
        border-radius: 8px;
        margin: 0 3px;
        border: 1px solid #e5e7eb;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border-color: #7A0019;
        color: white;
    }
    
    .pagination .page-link:hover {
        background: rgba(244, 197, 66, 0.2);
        color: #7A0019;
        border-color: #F4C542;
    }
    
    @media (max-width: 768px) {
        .stat-card h2 {
            font-size: 1.5rem;
        }
        
        .table th, .table td {
            padding: 10px;
            font-size: 0.8rem;
        }
        
        .status-badge {
            padding: 4px 8px;
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@endsection