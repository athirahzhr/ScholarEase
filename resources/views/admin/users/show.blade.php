@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i> User Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="row">
                        <!-- User Info Card -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                         style="width: 100px; height: 100px; font-size: 2.5rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h4>{{ $user->name }}</h4>
                                    <p class="text-muted">{{ $user->email }}</p>
                                    
                                    <div class="d-flex justify-content-center gap-2 mt-3">
                                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="mailto:{{ $user->email }}" class="btn btn-outline-primary">
                                            <i class="fas fa-envelope me-2"></i> Send Email
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100">
                                                <i class="fas fa-trash me-2"></i> Delete User
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Details -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">User Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">User ID</label>
                                            <p class="form-control-plaintext">{{ $user->id }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Account Status</label>
                                            <p>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Email Verified</label>
                                            <p>
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success">Verified on {{ $user->email_verified_at->format('d M Y') }}</span>
                                                @else
                                                    <span class="badge bg-warning">Not Verified</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Last Login</label>
                                            <p class="form-control-plaintext">
                                                @if($user->last_login_at)
                                                    {{ $user->last_login_at->diffForHumans() }}
                                                @else
                                                    <span class="text-muted">Never logged in</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Member Since</label>
                                            <p class="form-control-plaintext">{{ $user->created_at->format('d M Y, h:i A') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Last Updated</label>
                                            <p class="form-control-plaintext">{{ $user->updated_at->format('d M Y, h:i A') }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- User Profile (if exists) -->
                                    @if($user->profile)
                                    <hr>
                                    <h6 class="mb-3">User Profile</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Academic Category</label>
                                            <p class="form-control-plaintext">{{ $user->profile->academic_category ?? 'Not set' }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Income Category</label>
                                            <p class="form-control-plaintext">{{ $user->profile->income_category ?? 'Not set' }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Study Path</label>
                                            <p class="form-control-plaintext">{{ $user->profile->study_path ?? 'Not set' }}</p>
                                        </div>
                                        @if($user->profile->total_as)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Total A's</label>
                                            <p class="form-control-plaintext">{{ $user->profile->total_as }}</p>
                                        </div>
                                        @endif
                                        @if($user->profile->verified_at)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Profile Verified</label>
                                            <p class="form-control-plaintext">{{ $user->profile->verified_at->format('d M Y') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <div class="alert alert-info">
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
@endsection