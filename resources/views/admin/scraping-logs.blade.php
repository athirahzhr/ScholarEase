@extends('layouts.admin')

@section('title', 'Scraping Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Scraping Logs
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="runScraping()">
                            <i class="fas fa-sync-alt me-1"></i> Run Scraping Now
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshTable()">
                            <i class="fas fa-redo"></i>
                        </button>
                        <input type="text" id="searchInput" class="form-control form-control-sm w-auto" placeholder="Search logs...">
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Successful</h6>
                                            <h3 class="mb-0">{{ $logs->where('status', 'success')->count() }}</h3>
                                        </div>
                                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-start border-danger border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Failed</h6>
                                            <h3 class="mb-0">{{ $logs->where('status', 'failed')->count() }}</h3>
                                        </div>
                                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-start border-warning border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Total Scholarships</h6>
                                            <h3 class="mb-0">{{ $logs->sum('scholarships_added') }}</h3>
                                        </div>
                                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-start border-info border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Last Run</h6>
                                            <h5 class="mb-0">
                                                @if($logs->count() > 0)
                                                    {{ $logs->first()->created_at->diffForHumans() }}
                                                @else
                                                    Never
                                                @endif
                                            </h5>
                                        </div>
                                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="logsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Website</th>
                                    <th>Status</th>
                                    <th>Scholarships</th>
                                    <th>Started</th>
                                    <th>Duration</th>
                                    <th>Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @if(str_contains(strtolower($log->website_name), 'jpa'))
                                                    <i class="fas fa-university text-primary"></i>
                                                @elseif(str_contains(strtolower($log->website_name), 'mara'))
                                                    <i class="fas fa-landmark text-success"></i>
                                                @elseif(str_contains(strtolower($log->website_name), 'yayasan'))
                                                    <i class="fas fa-hands-helping text-warning"></i>
                                                @else
                                                    <i class="fas fa-globe text-info"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>{{ $log->website_name }}</strong>
                                                <div class="text-muted small">{{ $log->created_at->format('d M Y, H:i') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->status == 'success')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Success
                                            </span>
                                        @elseif($log->status == 'failed')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Failed
                                            </span>
                                        @elseif($log->status == 'running')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-spinner fa-spin me-1"></i> Running
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->scholarships_added > 0)
                                            <span class="badge bg-info">
                                                <i class="fas fa-plus me-1"></i>{{ $log->scholarships_added }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ $log->created_at->format('H:i') }}
                                            <div class="text-muted">{{ $log->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->duration)
                                            {{ $log->duration }}s
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small text-muted" style="max-width: 200px;">
                                            {{ Str::limit($log->details, 60) }}
                                            @if(strlen($log->details) > 60)
                                                <a href="javascript:void(0)" class="text-primary view-details" 
                                                   data-details="{{ $log->details }}">
                                                    View more
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-info view-log-btn" 
                                                    data-log-id="{{ $log->id }}" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($log->status == 'failed')
                                            <button type="button" class="btn btn-outline-warning retry-btn" 
                                                    data-log-id="{{ $log->id }}" title="Retry">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger delete-log-btn" 
                                                    data-log-id="{{ $log->id }}" title="Delete Log">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-robot fa-2x mb-3"></i>
                                            <p>No scraping logs found</p>
                                            <button class="btn btn-primary btn-sm" onclick="runScraping()">
                                                <i class="fas fa-sync-alt me-1"></i> Run First Scraping
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} logs
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scraping Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Website:</strong> <span id="detail-website"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> <span id="detail-status"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Started:</strong> <span id="detail-started"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Duration:</strong> <span id="detail-duration"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Scholarships Added:</strong> <span id="detail-scholarships"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong> <span id="detail-type"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Details:</strong>
                    <div class="card mt-2">
                        <div class="card-body">
                            <pre id="detail-full-details" class="mb-0" style="white-space: pre-wrap; font-family: inherit;"></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                <p>Are you sure you want to delete this scraping log?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Log</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#logsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
    
    // View details button
    $(document).on('click', '.view-log-btn', function() {
        var logId = $(this).data('log-id');
        viewLogDetails(logId);
    });
    
    // View details from text link
    $(document).on('click', '.view-details', function() {
        var details = $(this).data('details');
        $('#detail-full-details').text(details);
        $('#detailsModal').modal('show');
    });
    
    // Delete button
    $(document).on('click', '.delete-log-btn', function() {
        var logId = $(this).data('log-id');
        confirmDelete(logId);
    });
    
    // Retry button
    $(document).on('click', '.retry-btn', function() {
        var logId = $(this).data('log-id');
        retryScraping(logId);
    });
});

function viewLogDetails(logId) {
    // Fetch log details via AJAX
    fetch(`/admin/scraping-logs/${logId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#detail-website').text(data.log.website_name);
                $('#detail-status').html(getStatusBadge(data.log.status));
                $('#detail-started').text(new Date(data.log.created_at).toLocaleString());
                $('#detail-duration').text(data.log.duration ? data.log.duration + ' seconds' : 'N/A');
                $('#detail-scholarships').text(data.log.scholarships_added);
                $('#detail-type').text(data.log.type || 'Automatic');
                $('#detail-full-details').text(data.log.details || 'No details available');
                $('#detailsModal').modal('show');
            } else {
                alert('Failed to load log details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load log details');
        });
}

function getStatusBadge(status) {
    if (status === 'success') {
        return '<span class="badge bg-success">Success</span>';
    } else if (status === 'failed') {
        return '<span class="badge bg-danger">Failed</span>';
    } else if (status === 'running') {
        return '<span class="badge bg-warning">Running</span>';
    } else {
        return `<span class="badge bg-secondary">${status}</span>`;
    }
}

function confirmDelete(logId) {
    // Set the form action
    $('#deleteForm').attr('action', `/admin/scraping-logs/${logId}`);
    
    // Show the modal
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function runScraping() {
    if (confirm('Are you sure you want to run scraping now? This may take several minutes.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running...';
        button.disabled = true;
        
        // Make AJAX request
        fetch('{{ route("scrape.now") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            button.innerHTML = originalHTML;
            button.disabled = false;
            
            if (data.success) {
                alert('Scraping job has been queued successfully!');
                setTimeout(() => location.reload(), 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            button.innerHTML = originalHTML;
            button.disabled = false;
            alert('An error occurred. Please try again.');
        });
    }
}

function retryScraping(logId) {
    if (confirm('Retry this scraping job?')) {
        fetch(`/admin/scraping-logs/${logId}/retry`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Scraping job has been queued for retry!');
                setTimeout(() => location.reload(), 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}

function refreshTable() {
    location.reload();
}
</script>
@endpush
@endsection