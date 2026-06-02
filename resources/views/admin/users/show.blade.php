@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i> User Details
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-edit btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit User
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="row">
                        <!-- User Info Card -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <div class="avatar-circle mx-auto mb-3">
                                        @if($user->profile && $user->profile->avatar)
                                            <img src="{{ asset('storage/' . $user->profile->avatar) }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="rounded-circle" 
                                                 width="100" 
                                                 height="100"
                                                 style="object-fit: cover; border: 3px solid #F4C542;">
                                        @else
                                            <div class="avatar-initial rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                                 style="width: 100px; height: 100px; background: linear-gradient(135deg, #7A0019, #4e0010); color: white; font-weight: 700; font-size: 2.5rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <h4 style="color: #7A0019; font-weight: 700;">{{ $user->name }}</h4>
                                    <p class="text-muted">
                                        <i class="fas fa-envelope me-1"></i> {{ $user->email }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-center gap-2 mt-3">
                                        @if($user->role === 'admin')
                                            <span class="role-badge role-admin">
                                                <i class="fas fa-crown me-1"></i> Administrator
                                            </span>
                                        @else
                                            <span class="role-badge role-user">
                                                <i class="fas fa-user me-1"></i> Regular User
                                            </span>
                                        @endif
                                        <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                            <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i> Quick Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="mailto:{{ $user->email }}" class="btn btn-action-email">
                                            <i class="fas fa-envelope me-2"></i> Send Email
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                              id="deleteUserForm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-action-delete w-100" onclick="confirmUserDelete()">
                                                <i class="fas fa-trash-alt me-2"></i> Delete User
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Stats -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i> Account Stats
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="stat-item mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">
                                                <i class="fas fa-bookmark me-2" style="color: #7A0019;"></i> Bookmarks
                                            </span>
                                            <span class="fw-bold" style="color: #7A0019;">{{ $user->bookmarks()->count() ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="stat-item mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">
                                                <i class="fas fa-clock me-2" style="color: #7A0019;"></i> Last Login
                                            </span>
                                            <span class="small">
                                                @if($user->last_login_at)
                                                    {{ $user->last_login_at->diffForHumans() }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">
                                                <i class="fas fa-calendar-alt me-2" style="color: #7A0019;"></i> Member Since
                                            </span>
                                            <span class="small">{{ $user->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Details -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i> User Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-id-card me-2" style="color: #7A0019;"></i> User ID
                                            </label>
                                            <div class="info-value">
                                                <span class="badge" style="background: #f3f4f6; color: #4b5563;">#{{ $user->id }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-power-off me-2" style="color: #7A0019;"></i> Account Status
                                            </label>
                                            <div>
                                                <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                                    <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-check-circle me-2" style="color: #7A0019;"></i> Email Verified
                                            </label>
                                            <div>
                                                @if($user->email_verified_at)
                                                    <span class="badge" style="background: #d1fae5; color: #065f46;">
                                                        <i class="fas fa-check-circle me-1"></i> Verified on {{ $user->email_verified_at->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: #fef3c7; color: #92400e;">
                                                        <i class="fas fa-clock me-1"></i> Not Verified
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-clock me-2" style="color: #7A0019;"></i> Last Login
                                            </label>
                                            <div class="info-value">
                                                @if($user->last_login_at)
                                                    <i class="fas fa-history me-1 text-muted"></i>
                                                    {{ $user->last_login_at->format('d M Y, h:i A') }}
                                                    <span class="text-muted small">({{ $user->last_login_at->diffForHumans() }})</span>
                                                @else
                                                    <span class="text-muted">Never logged in</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-calendar-plus me-2" style="color: #7A0019;"></i> Member Since
                                            </label>
                                            <div class="info-value">
                                                {{ $user->created_at->format('d M Y, h:i A') }}
                                                <span class="text-muted small">({{ $user->created_at->diffForHumans() }})</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-calendar-edit me-2" style="color: #7A0019;"></i> Last Updated
                                            </label>
                                            <div class="info-value">
                                                {{ $user->updated_at->format('d M Y, h:i A') }}
                                                <span class="text-muted small">({{ $user->updated_at->diffForHumans() }})</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- User Profile Section -->
                                    @if($user->profile)
                                        <hr class="my-4" style="border-color: #F4C542;">
                                        <h6 class="mb-3 fw-semibold" style="color: #7A0019;">
                                            <i class="fas fa-user-circle me-2"></i> User Profile
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Academic Category</label>
                                                <div class="info-value">
                                                    @if($user->profile->academic_category)
                                                        <span class="badge" style="background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(122,0,25,0.05)); color: #7A0019;">
                                                            {{ $user->profile->academic_category }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not set</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Income Category</label>
                                                <div class="info-value">
                                                    @if($user->profile->income_category)
                                                        <span class="badge" style="background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.08)); color: #92400e;">
                                                            {{ $user->profile->income_category }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not set</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Study Path</label>
                                                <div class="info-value">
                                                    @if($user->profile->study_path)
                                                        <span class="badge" style="background: #dbeafe; color: #1e40af;">
                                                            {{ $user->profile->study_path }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not set</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($user->profile->total_as)
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">SPM Results</label>
                                                    <div class="info-value">
                                                        <span class="badge" style="background: linear-gradient(135deg, #7A0019, #4e0010); color: white;">
                                                            <i class="fas fa-star me-1"></i> {{ $user->profile->total_as }} A's
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($user->profile->verified_at)
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Profile Verified</label>
                                                    <div class="info-value">
                                                        <span class="badge" style="background: #d1fae5; color: #065f46;">
                                                            <i class="fas fa-certificate me-1"></i> {{ $user->profile->verified_at->format('d M Y') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            This user hasn't completed their profile yet.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2" style="color: #f59e0b;"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong>"{{ $user->name }}"</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All user data will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i> Delete Permanently
                </button>
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
    }
    
    .card-header h5, .card-header h6 {
        color: #7A0019;
        font-weight: 700;
    }
    
    .avatar-initial {
        box-shadow: 0 4px 15px rgba(122, 0, 25, 0.2);
    }
    
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .role-admin {
        background: linear-gradient(135deg, #7A0019, #4e0010);
        color: white;
    }
    
    .role-user {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 40px;
        font-size: 0.75rem;
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
    
    .form-label {
        color: #374151;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .info-value {
        color: #4b5563;
        padding: 4px 0;
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
    
    .btn-action-email {
        background: linear-gradient(115deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.6rem 1rem;
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
    }
    
    .btn-action-email:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        color: white;
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
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-left: 4px solid #10b981;
        border-radius: 12px;
        color: #065f46;
    }
    
    .alert-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border: none;
        border-left: 4px solid #3b82f6;
        border-radius: 12px;
        color: #1e40af;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: none;
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        color: #92400e;
    }
    
    .stat-item {
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .stat-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .modal-content {
        border-radius: 20px;
        border: none;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-bottom: 2px solid #F4C542;
        border-radius: 20px 20px 0 0;
    }
    
    .modal-title {
        color: #7A0019;
        font-weight: 700;
    }
    
    .btn-danger {
        background: linear-gradient(115deg, #dc2626, #b91c1c);
        border: none;
        border-radius: 40px;
        transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
    }
    
    hr {
        opacity: 0.5;
    }
    
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header h5 {
            margin-bottom: 10px;
        }
        
        .avatar-initial {
            width: 80px !important;
            height: 80px !important;
            font-size: 2rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmUserDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        document.getElementById('deleteUserForm').submit();
    });
</script>
@endpush

@endsection