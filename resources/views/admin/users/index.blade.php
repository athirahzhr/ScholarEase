@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Manage Users</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <input type="text" id="searchInput" class="form-control form-control-sm w-auto" placeholder="Search users...">
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                @if($user->profile && $user->profile->avatar)
                                                    <img src="{{ asset('storage/' . $user->profile->avatar) }}" 
                                                         alt="{{ $user->name }}" 
                                                         class="rounded-circle" 
                                                         width="40" 
                                                         height="40">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <div class="text-muted small">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-danger">Admin</span>
                                        @else
                                            <span class="badge bg-primary">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ $user->created_at->format('d M Y') }}
                                            <div class="text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                        </div>
                                    </td>
                                  
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            {{-- View Profile --}}
                                            <a href="{{ route('admin.users.show', $user->id) }}" 
                                               class="btn btn-outline-info" title="View Profile">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            {{-- Edit User --}}
                                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                                               class="btn btn-outline-warning" title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            {{-- Delete User (only for non-admins and not yourself) --}}
                                            @if($user->role !== 'admin' && $user->id != auth()->id())
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDelete('{{ $user->id }}')" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-user-slash fa-2x mb-3"></i>
                                            <p>No users found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </div>
                        <div>
                            {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    All user data including bookmarks and applications will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Hide DataTables pagination and info */
    .dataTables_paginate, .dataTables_info {
        display: none !important;
    }
    
    /* Avatar styling */
    .avatar img {
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable WITHOUT pagination
    var table = $('#usersTable').DataTable({
        paging: false,          // Disable DataTables pagination
        info: false,            // Hide "Showing X to Y of Z entries"
        lengthChange: false,    // Hide "Show X entries" dropdown
        searching: true,        // Keep search enabled
        ordering: true,         // Keep sorting enabled
        columnDefs: [
            { orderable: false, targets: [5] } // Disable sorting on actions column
        ]
    });
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
});

function confirmDelete(userId) {
    // Set the form action - use the named route
    $('#deleteForm').attr('action', '{{ url("admin/users") }}/' + userId);
    
    // Show the modal
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function refreshTable() {
    location.reload();
}
</script>
@endpush
@endsection