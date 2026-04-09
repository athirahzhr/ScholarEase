@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h4>
                <div class="text-muted">
                    {{ now()->format('l, F j, Y') }}
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Users</h6>
                                    <h3 class="mb-0">{{ $totalUsers ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Scholarships</h6>
                                    <h3 class="mb-0">{{ $totalScholarships ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Bookmarks</h6>
                                    <h3 class="mb-0">{{ $totalBookmarks ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-bookmark fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Recent Users</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($recentUsers) && $recentUsers->count() > 0)
                            <div class="list-group">
                                @foreach($recentUsers as $user)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $user->name }}</h6>
                                        <small>{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $user->email }}</p>
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-muted">No recent users</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Recent Scholarships</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($recentScholarships) && $recentScholarships->count() > 0)
                            <div class="list-group">
                                @foreach($recentScholarships as $scholarship)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ Str::limit($scholarship->title, 40) }}</h6>
                                        <small>{{ $scholarship->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $scholarship->provider }}</p>
                                    <span class="badge {{ $scholarship->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-muted">No recent scholarships</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection