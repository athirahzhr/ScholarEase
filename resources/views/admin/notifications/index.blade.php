@extends('layouts.admin')

@section('title', 'Notification Centre')

@section('content')

<!-- STATS CARDS -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-3">
                <i class="fas fa-bookmark"></i>
            </div>
            <h3 class="mb-1">{{ $totalBookmarks }}</h3>
            <p class="text-muted mb-0">Total Bookmarks</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-3" style="background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.05));">
                <i class="fas fa-clock" style="color: #f59e0b;"></i>
            </div>
            <h3 class="mb-1" style="color: #f59e0b;">{{ $pending }}</h3>
            <p class="text-muted mb-0">Pending</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-3" style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
            </div>
            <h3 class="mb-1" style="color: #10b981;">{{ $sent }}</h3>
            <p class="text-muted mb-0">Success</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-3" style="background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05));">
                <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
            </div>
            <h3 class="mb-1" style="color: #ef4444;">{{ $failed }}</h3>
            <p class="text-muted mb-0">Failed</p>
        </div>
    </div>
</div>

<!-- ACTION BUTTONS -->
<div class="mb-4 d-flex gap-2">
    <form method="POST" action="{{ route('admin.notifications.all') }}">
        @csrf
        <button class="btn btn-primary" style="background: linear-gradient(115deg, #7A0019, #4e0010);">
            <i class="fas fa-paper-plane me-2"></i> Send All Pending
        </button>
    </form>
</div>

<!-- TABS -->
<ul class="nav nav-tabs mb-3" style="border-bottom-color: #e5e7eb;">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#pending" style="color: #7A0019; font-weight: 600; border-radius: 12px 12px 0 0;">
            <i class="fas fa-clock me-2"></i> Pending
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#history" style="color: #6b7280; font-weight: 600; border-radius: 12px 12px 0 0;">
            <i class="fas fa-history me-2"></i> History
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- PENDING TAB -->
    <div class="tab-pane fade show active" id="pending">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bell me-2"></i> Pending Notifications
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>User</th>
                                <th><i class="fas fa-graduation-cap me-2"></i>Scholarship</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>Deadline</th>
                                <th><i class="fas fa-tag me-2"></i>Status</th>
                                <th><i class="fas fa-cog me-2"></i>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingList as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->user->name) }}&background=7A0019&color=F4C542&bold=true" 
                                                 style="width: 32px; height: 32px; border-radius: 50%;">
                                        </div>
                                        <div>
                                            <strong>{{ $item->user->name }}</strong><br>
                                            <small class="text-muted">{{ $item->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ Str::limit($item->scholarship->title, 50) }}</strong><br>
                                    <small class="text-muted">{{ $item->scholarship->provider }}</small>
                                </td>
                                <td>
                                    @if($item->scholarship->deadline)
                                        <span class="badge" style="background: #fef3c7; color: #92400e;">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($item->scholarship->deadline)->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="badge" style="background: #d1fae5; color: #065f46;">
                                            <i class="fas fa-infinity me-1"></i> Rolling
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: #fef3c7; color: #92400e; padding: 6px 12px;">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.notifications.single') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="bookmark_id" value="{{ $item->id }}">
                                        <button class="btn btn-sm" style="background: linear-gradient(115deg, #7A0019, #4e0010); color: white; border-radius: 40px;">
                                            <i class="fas fa-paper-plane me-1"></i> Send Now
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-bell-slash" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                    <h5 class="text-muted">No Pending Notifications</h5>
                                    <p class="text-muted">All caught up! No notifications waiting to be sent.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- HISTORY TAB -->
    <div class="tab-pane fade" id="history">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history me-2"></i> Notification History
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>User ID</th>
                                <th><i class="fas fa-tag me-2"></i>Type</th>
                                <th><i class="fas fa-info-circle me-2"></i>Data</th>
                                <th><i class="fas fa-calendar me-2"></i>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                            <tr>
                                <td>
                                    <span class="badge" style="background: linear-gradient(135deg, #7A0019, #4e0010); color: white; padding: 6px 12px;">
                                        <i class="fas fa-id-card me-1"></i> #{{ $item->notifiable_id }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $typeClass = match($item->type) {
                                            'App\\Notifications\\ScholarshipDeadlineReminder' => 'warning',
                                            'App\\Notifications\\NewScholarshipAvailable' => 'success',
                                            default => 'info'
                                        };
                                    @endphp
                                    <span class="badge" style="background: #dbeafe; color: #1e40af;">
                                        <i class="fas fa-bell me-1"></i> {{ class_basename($item->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 400px;">
                                        @php
                                            $data = json_decode($item->data, true);
                                            echo $data['message'] ?? \Illuminate\Support\Str::limit($item->data, 80);
                                        @endphp
                                    </div>
                                </td>
                                <td>
                                    <span style="color: #6b7280; font-size: 0.85rem;">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                    <h5 class="text-muted">No History Found</h5>
                                    <p class="text-muted">No notifications have been sent yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(244,197,66,0.1));
        color: #7A0019;
    }
    
    .stat-card h3 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .nav-tabs .nav-link {
        border: none;
        padding: 12px 24px;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        background: rgba(122, 0, 25, 0.05);
        color: #7A0019;
        border: none;
    }
    
    .nav-tabs .nav-link.active {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
    }
    
    .table th {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        color: #7A0019;
        font-weight: 700;
        border-bottom: 2px solid #F4C542;
        padding: 15px;
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
    
    .avatar-sm img {
        border: 2px solid #F4C542;
    }
    
    .btn-sm {
        padding: 6px 16px;
        font-size: 0.85rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-bottom: 2px solid #F4C542;
        padding: 15px 20px;
        font-weight: 700;
        color: #7A0019;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .stat-card h3 {
            font-size: 1.5rem;
        }
        
        .table th, .table td {
            padding: 10px;
            font-size: 0.85rem;
        }
        
        .nav-tabs .nav-link {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        .btn-sm {
            padding: 4px 12px;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@endsection