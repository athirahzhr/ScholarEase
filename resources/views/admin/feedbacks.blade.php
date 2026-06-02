@extends('layouts.admin')

@section('title', 'Manage Feedbacks')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2" style="color: #F4C542;"></i>
                        Manage Feedbacks
                    </h5>
                    <div>
                        <span class="badge" style="background: #fef3c7; color: #92400e;">
                            <i class="fas fa-clock me-1"></i> Pending: {{ $pendingCount }}
                        </span>
                        <span class="badge ms-2" style="background: #d1fae5; color: #065f46;">
                            <i class="fas fa-check-circle me-1"></i> Approved: {{ $approvedCount }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success m-3">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="feedbacksTable">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th width="15%">User</th>
                                    <th width="10%">Rating</th>
                                    <th width="40%">Feedback</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks as $feedback)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="feedback-checkbox" value="{{ $feedback->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initial rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 35px; height: 35px; background: linear-gradient(135deg, #7A0019, #4e0010); color: white; font-weight: 700;">
                                                {{ strtoupper(substr($feedback->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $feedback->user->name ?? 'Unknown User' }}</div>
                                                <small class="text-muted">{{ $feedback->user->email ?? 'No email' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stars">
                                            @for($i = 1; $i <= $feedback->rating; $i++)
                                                ⭐
                                            @endfor
                                            @for($i = $feedback->rating + 1; $i <= 5; $i++)
                                                ☆
                                            @endfor
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 300px;">
                                            <p class="mb-0 small">{{ Str::limit($feedback->comment, 150) }}</p>
                                            @if(strlen($feedback->comment) > 150)
                                                <button class="btn btn-link btn-sm p-0 mt-1 view-full" data-comment="{{ $feedback->comment }}">Read more</button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($feedback->approved)
                                            <span class="badge" style="background: #d1fae5; color: #065f46;">
                                                <i class="fas fa-check-circle me-1"></i> Approved
                                            </span>
                                        @else
                                            <span class="badge" style="background: #fef3c7; color: #92400e;">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ $feedback->created_at->format('d M Y') }}
                                            <div class="text-muted">{{ $feedback->created_at->diffForHumans() }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if(!$feedback->approved)
                                                <form action="{{ route('admin.feedbacks.approve', $feedback->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.feedbacks.reject', $feedback->id) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <td>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-star" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                        <h5 class="text-muted">No Feedbacks Yet</h5>
                                        <p class="text-muted">Feedbacks from users will appear here.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <button class="btn btn-sm btn-primary" id="bulkApproveBtn" style="display: none;">
                                    <i class="fas fa-check-double me-1"></i> Approve Selected
                                </button>
                            </div>
                            <div>
                                {{ $feedbacks->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="fullComment"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(244, 197, 66, 0.08);
    }
    
    .stars {
        font-size: 0.9rem;
        color: #F4C542;
    }
    
    .btn-sm {
        border-radius: 8px;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Select All functionality
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.feedback-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            toggleBulkApproveBtn();
        });
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkApproveBtn);
    });
    
    function toggleBulkApproveBtn() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkApproveBtn.style.display = anyChecked ? 'inline-block' : 'none';
    }
    
    // Bulk Approve
    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', function() {
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) return;
            
            if (confirm(`Approve ${selectedIds.length} feedback(s)?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.feedbacks.bulk-approve") }}';
                form.innerHTML = `
                    @csrf
                    <input type="hidden" name="ids" value='${JSON.stringify(selectedIds)}'>
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // View full comment modal
    document.querySelectorAll('.view-full').forEach(btn => {
        btn.addEventListener('click', function() {
            const comment = this.dataset.comment;
            document.getElementById('fullComment').textContent = comment;
            new bootstrap.Modal(document.getElementById('commentModal')).show();
        });
    });
</script>
@endpush
@endsection