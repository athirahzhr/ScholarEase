@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i> Manage Users
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="refreshTable()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <input type="text" id="searchInput" class="form-control form-control-sm w-auto" placeholder="Search users by name, email or role...">
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 30%">User</th>
                                    <th style="width: 25%">Email</th>
                                    <th style="width: 10%">Role</th>
                                    <th style="width: 15%">Joined</th>
                                    <th style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <span class="badge" style="background: #f3f4f6; color: #4b5563;">
                                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                @if($user->profile && $user->profile->avatar)
                                                    <img src="{{ asset('storage/' . $user->profile->avatar) }}" 
                                                         alt="{{ $user->name }}" 
                                                         class="rounded-circle" 
                                                         width="45" 
                                                         height="45"
                                                         style="object-fit: cover; border: 2px solid #F4C542;">
                                                @else
                                                    <div class="avatar-initial rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 45px; height: 45px; background: linear-gradient(135deg, #7A0019, #4e0010); color: white; font-weight: 700; font-size: 1.2rem;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <strong style="color: #7A0019;">{{ $user->name }}</strong>
                                                <div class="text-muted small">
                                                    <i class="fas fa-id-card me-1"></i>ID: {{ $user->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-envelope me-1" style="color: #7A0019;"></i>
                                            {{ $user->email }}
                                        </div>
                                        @if($user->email_verified_at)
                                            <span class="badge" style="background: #d1fae5; color: #065f46; font-size: 0.7rem; margin-top: 4px;">
                                                <i class="fas fa-check-circle me-1"></i> Verified
                                            </span>
                                        @else
                                            <span class="badge" style="background: #fee2e2; color: #991b1b; font-size: 0.7rem; margin-top: 4px;">
                                                <i class="fas fa-clock me-1"></i> Unverified
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="role-badge role-admin">
                                                <i class="fas fa-crown me-1"></i> Admin
                                            </span>
                                        @else
                                            <span class="role-badge role-user">
                                                <i class="fas fa-user me-1"></i> User
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $user->created_at->format('d M Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $user->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.show', $user->id) }}" 
                                               class="btn btn-action btn-view" 
                                               title="View Profile">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                                               class="btn btn-action btn-edit" 
                                               title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($user->role !== 'admin' && $user->id != auth()->id())
                                                <button type="button" class="btn btn-action btn-delete" 
                                                        onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" 
                                                        title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-users-slash" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                        <h5 class="text-muted">No Users Found</h5>
                                        <p class="text-muted">Users will appear here once they register.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($users->hasPages())
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                                    </small>
                                </div>
                                <div>
                                    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    @endif
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
                <p id="deleteModalMessage">Are you sure you want to delete this user?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All user data including:
                    <ul class="mt-2 mb-0">
                        <li>Bookmarked scholarships</li>
                        <li>Application history</li>
                        <li>Profile information</li>
                        <li>Notification preferences</li>
                    </ul>
                    will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i> Delete Permanently
                    </button>
                </form>
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
    
    .card-header h5 {
        color: #7A0019;
        font-weight: 700;
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
    
    /* Role Badges */
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
    
    /* Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .btn-view {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    .btn-view:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-edit {
        background: rgba(122, 0, 25, 0.1);
        color: #7A0019;
        border: 1px solid rgba(122, 0, 25, 0.2);
    }
    
    .btn-edit:hover {
        background: #7A0019;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
        border: 1px solid rgba(220, 38, 38, 0.2);
    }
    
    .btn-delete:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-outline-secondary {
        border: 1px solid #e5e7eb;
        color: #6b7280;
        border-radius: 8px;
    }
    
    .btn-outline-secondary:hover {
        background: #7A0019;
        border-color: #7A0019;
        color: white;
    }
    
    .form-control-sm {
        border-radius: 40px;
        border: 2px solid #e5e7eb;
        padding: 0.4rem 1rem;
        width: 250px;
    }
    
    .form-control-sm:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-left: 4px solid #10b981;
        border-radius: 12px;
        color: #065f46;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: none;
        border-left: 4px solid #dc2626;
        border-radius: 12px;
        color: #991b1b;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: none;
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        color: #92400e;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background: white;
        padding: 15px 20px;
    }
    
    .avatar-initial {
        box-shadow: 0 2px 8px rgba(122, 0, 25, 0.2);
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
    
    .btn-secondary {
        background: #6b7280;
        border: none;
        border-radius: 40px;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
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
    
    /* Hide DataTable's default search box */
    .dataTables_filter {
        display: none !important;
    }
    
    @media (max-width: 768px) {
        .table th, .table td {
            padding: 10px;
            font-size: 0.8rem;
        }
        
        .avatar img, .avatar-initial {
            width: 35px !important;
            height: 35px !important;
            font-size: 0.9rem !important;
        }
        
        .btn-action {
            width: 28px;
            height: 28px;
        }
        
        .role-badge {
            font-size: 0.65rem;
            padding: 3px 8px;
        }
        
        .form-control-sm {
            width: 180px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable WITHOUT pagination and WITHOUT default search box
    var table = $('#usersTable').DataTable({
        paging: false,
        info: false,
        lengthChange: false,
        searching: true,
        ordering: true,
        columnDefs: [
            { orderable: false, targets: [5] } // Disable sorting on actions column
        ],
        // Disable the default search box DOM element
        dom: 't',
        language: {
            search: "",
            searchPlaceholder: ""
        }
    });
    
    // Custom search functionality using our input
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
});

let userNameToDelete = '';

function confirmDelete(userId, userName) {
    userNameToDelete = userName;
    // Set the form action
    $('#deleteForm').attr('action', '{{ url("admin/users") }}/' + userId);
    $('#deleteModalMessage').html(`Are you sure you want to delete user <strong>"${userName}"</strong>?`);
    
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