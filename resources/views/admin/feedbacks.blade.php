@extends('layouts.admin')

@section('title', 'Student Feedbacks')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2" style="color: #F4C542;"></i>
                        Student Feedbacks
                    </h5>
                    <div>
                        <span class="badge bg-primary">
                        <i class="fas fa-comments me-1"></i>
                        Total Feedback: {{ $feedbacks->total() }}
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
                                    <th width="15%">User</th>
                                    <th width="10%">Rating</th>
                                    <th width="40%">Feedback</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks as $feedback)
                                <tr>
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
                                        <div class="small">
                                            {{ $feedback->created_at->format('d M Y') }}
                                            <div class="text-muted">{{ $feedback->created_at->diffForHumans() }}</div>
                                        </div>
                                    </td>

                                    <td>
                                        <form action="{{ route('admin.feedbacks.delete', $feedback->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this feedback?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    title="Delete Feedback">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <td>
                                    <td colspan="4" class="text-center py-5">
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