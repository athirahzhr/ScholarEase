@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i> Edit User: {{ $user->name }}
                    </h5>
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to User
                    </a>
                </div>

                <div class="card-body">

                    {{-- MAIN UPDATE FORM --}}
                    <form id="update-user-form"
                          action="{{ route('admin.users.update', $user->id) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            {{-- Name --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-user me-2" style="color: #7A0019;"></i>
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                       class="form-control"
                                       value="{{ old('name', $user->name) }}" 
                                       placeholder="Enter full name"
                                       required>
                                <small class="text-muted">User's full name as displayed in the system</small>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2" style="color: #7A0019;"></i>
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email"
                                       class="form-control"
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="user@example.com"
                                       required>
                                <small class="text-muted">User's email address for login and notifications</small>
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2" style="color: #7A0019;"></i>
                                    New Password
                                </label>
                                <input type="password" name="password"
                                       class="form-control"
                                       placeholder="Enter new password (optional)">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Leave blank to keep current password. Minimum 8 characters if changed.
                                </small>
                            </div>

                            {{-- Password Confirm --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-check-circle me-2" style="color: #7A0019;"></i>
                                    Confirm New Password
                                </label>
                                <input type="password" name="password_confirmation"
                                       class="form-control"
                                       placeholder="Confirm new password">
                                <small class="text-muted">Re-enter the new password to confirm</small>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-user-tag me-2" style="color: #7A0019;"></i>
                                    User Role <span class="text-danger">*</span>
                                </label>
                                <select name="role" class="form-select" required>
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>
                                        <i class="fas fa-user me-2"></i> Regular User
                                    </option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                        <i class="fas fa-crown me-2"></i> Administrator
                                    </option>
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Administrators have full access to the admin panel
                                </small>
                            </div>

                            {{-- Active Status --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-power-off me-2" style="color: #7A0019;"></i>
                                    Account Status
                                </label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox"
                                           name="is_active"
                                           class="form-check-input"
                                           value="1"
                                           id="activeSwitch"
                                           {{ $user->is_active ? 'checked' : '' }}
                                           style="cursor: pointer;">
                                    <label class="form-check-label fw-medium" for="activeSwitch" style="cursor: pointer;">
                                        <span id="activeStatusLabel" class="badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                            <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                            {{ $user->is_active ? 'Active Account' : 'Inactive Account' }}
                                        </span>
                                    </label>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Inactive users cannot log in to the system
                                </small>
                            </div>
                        </div>
                    </form>

                    {{-- DANGER ZONE (SEPARATE FORM) --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2" style="color: #dc2626;"></i>
                                Danger Zone
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> This action is irreversible. All user data including bookmarks and preferences will be permanently deleted.
                            </div>

                            <form id="delete-user-form"
                                  action="{{ route('admin.users.destroy', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt me-2"></i> Delete User Permanently
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>

                        <button type="submit"
                                form="update-user-form"
                                class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
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
    }
    
    .card-header h5, .card-header h6 {
        color: #7A0019;
        font-weight: 700;
    }
    
    .form-label {
        color: #374151;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
        outline: none;
    }
    
    .form-check-input:checked {
        background-color: #7A0019;
        border-color: #7A0019;
    }
    
    .form-check-input:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 0.2rem rgba(244, 197, 66, 0.25);
    }
    
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
    }
    
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        padding: 0.625rem 1.5rem;
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
    
    .btn-secondary {
        background: #6b7280;
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 60px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }
    
    .btn-danger {
        background: linear-gradient(115deg, #dc2626, #b91c1c);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
    }
    
    .btn-sm {
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: none;
        border-left: 4px solid #dc2626;
        border-radius: 16px;
        color: #991b1b;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: none;
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        color: #92400e;
    }
    
    .status-active {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        padding: 4px 12px;
        border-radius: 40px;
        font-weight: 600;
    }
    
    .status-inactive {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        padding: 4px 12px;
        border-radius: 40px;
        font-weight: 600;
    }
    
    .text-muted {
        color: #6b7280 !important;
        font-size: 0.8rem;
    }
    
    .fw-semibold {
        font-weight: 600;
    }
    
    .text-danger {
        color: #dc2626 !important;
    }
    
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header h5 {
            margin-bottom: 10px;
        }
        
        .form-control, .form-select {
            padding: 8px 12px;
        }
        
        .btn-primary, .btn-secondary {
            padding: 0.5rem 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Update status label when switch is toggled
    const activeSwitch = document.getElementById('activeSwitch');
    const statusLabel = document.getElementById('activeStatusLabel');
    
    if (activeSwitch) {
        activeSwitch.addEventListener('change', function() {
            if (this.checked) {
                statusLabel.innerHTML = '<i class="fas fa-check-circle me-1"></i> Active Account';
                statusLabel.className = 'badge status-active';
            } else {
                statusLabel.innerHTML = '<i class="fas fa-ban me-1"></i> Inactive Account';
                statusLabel.className = 'badge status-inactive';
            }
        });
    }
    
</script>
@endpush

@endsection