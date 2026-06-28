@extends('layouts.admin')

@section('title', 'Manage Scholarships')

@section('content')
<div class="container-fluid px-0">

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Manage Scholarships
                    </h5>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.scholarships.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Add New
                        </a>

                        <input type="text" id="searchInput" class="form-control form-control-sm w-auto" placeholder="Search scholarships...">
                    </div>
                </div>

                {{-- BODY --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="scholarshipsTable">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">Title</th>
                                    <th style="width: 12%">Provider</th>
                                    <th style="width: 35%">Eligibility Rules</th>
                                    <th style="width: 10%">Deadline</th>
                                    <th style="width: 8%">Status</th>
                                    <th style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($scholarships as $scholarship)
                                @php
                                    $c = $scholarship->eligibilityCriteria;
                                @endphp
                                <tr>
                                    {{-- NUMBER --}}
                                    <td>
                                        <span class="badge" style="background: #f3f4f6; color: #4b5563;">
                                            {{ ($scholarships->currentPage() - 1) * $scholarships->perPage() + $loop->iteration }}
                                        </span>
                                    </td>

                                    {{-- TITLE --}}
                                    <td>
                                        <div class="fw-semibold" style="color: #7A0019;">
                                            {{ Str::limit($scholarship->title, 40) }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-id-card me-1"></i>ID: {{ $scholarship->id }}
                                        </div>
                                    </td>

                                    {{-- PROVIDER --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="provider-icon me-2">
                                                <i class="fas fa-building" style="color: #7A0019;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $scholarship->provider }}</div>
                                                @if($scholarship->source)
                                                    <small class="text-muted">Source: {{ ucfirst($scholarship->source) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ELIGIBILITY --}}
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($c)
                                                {{-- MIN SPM --}}
                                                @if($c->min_spm_as)
                                                    <span class="eligibility-badge eligibility-maroon">
                                                        <i class="fas fa-star me-1"></i> Min {{ $c->min_spm_as }} A's
                                                    </span>
                                                @endif

                                                {{-- MAX SPM --}}
                                                @if($c->max_spm_as)
                                                    <span class="eligibility-badge eligibility-dark">
                                                        <i class="fas fa-star me-1"></i> Max {{ $c->max_spm_as }} A's
                                                    </span>
                                                @endif

                                                {{-- MAX INCOME --}}
                                                @if($c->max_monthly_income)
                                                    <span class="eligibility-badge eligibility-success">
                                                        <i class="fas fa-money-bill-wave me-1"></i> ≤ RM {{ number_format($c->max_monthly_income, 0) }}
                                                    </span>
                                                @endif

                                                                                                {{-- INCOME CATEGORY --}}
                                                @php
                                                    $incomeCategories = $c->income_categories;

                                                    if (is_string($incomeCategories)) {
                                                        $incomeCategories = json_decode($incomeCategories, true);
                                                    }
                                                @endphp

                                                @if(!empty($incomeCategories))

                                                    @foreach($incomeCategories as $category)

                                                        <span class="eligibility-badge eligibility-success">
                                                            <i class="fas fa-users me-1"></i>
                                                            {{ $category }}
                                                        </span>

                                                    @endforeach

                                                @endif

                                                {{-- STUDY LEVEL --}}
                                                @foreach(($c->study_paths ?? []) as $path)
                                                    <span class="eligibility-badge eligibility-gold">
                                                        <i class="fas fa-graduation-cap me-1"></i> {{ $path }}
                                                    </span>
                                                @endforeach

                                                {{-- FIELD OF STUDY --}}
                                                @foreach(($c->fields_of_study ?? []) as $field)
                                                    <span class="eligibility-badge eligibility-info">
                                                        <i class="fas fa-book me-1"></i> {{ Str::limit($field, 20) }}
                                                    </span>
                                                @endforeach

                                                {{-- CITIZENSHIP --}}
                                                @if($c->citizenship_required)
                                                    <span class="eligibility-badge eligibility-dark">
                                                        <i class="fas fa-passport me-1"></i> {{ $c->citizenship_required }}
                                                    </span>
                                                @endif

                                                {{-- STATE --}}
                                                @if($c->state_requirement)
                                                    <span class="eligibility-badge eligibility-maroon">
                                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $c->state_requirement }}
                                                    </span>
                                                @endif

                                                {{-- AGE --}}
                                                @if($c->min_age || $c->max_age)
                                                    <span class="eligibility-badge eligibility-warning">
                                                        <i class="fas fa-birthday-cake me-1"></i> Age: {{ $c->min_age ?? '-' }} - {{ $c->max_age ?? '-' }}
                                                    </span>
                                                @endif

                                                {{-- BUMIPUTERA --}}
                                                @if($c->bumiputera_required)
                                                    <span class="eligibility-badge eligibility-danger">
                                                        <i class="fas fa-star-of-life me-1"></i> Bumiputera Required
                                                    </span>
                                                @elseif($c->bumiputera_priority)
                                                    <span class="eligibility-badge eligibility-secondary">
                                                        <i class="fas fa-star-of-life me-1"></i> Bumiputera Priority
                                                    </span>
                                                @endif

                                            @else
                                                <span class="eligibility-badge eligibility-secondary">
                                                    <i class="fas fa-info-circle me-1"></i> No eligibility rules
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- DEADLINE --}}
                                    <td>
                                        @if($scholarship->deadline)
                                            <div class="fw-semibold">
                                                {{ $scholarship->deadline->format('d M Y') }}
                                            </div>
                                            <div class="small {{ $scholarship->deadline->isPast() ? 'text-danger' : 'text-success' }}">
                                                <i class="fas {{ $scholarship->deadline->isPast() ? 'fa-clock' : 'fa-calendar-check' }} me-1"></i>
                                                {{ $scholarship->deadline->isPast() ? 'Expired' : $scholarship->deadline->diffForHumans() }}
                                            </div>
                                        @else
                                            <span class="badge" style="background: #d1fae5; color: #065f46;">
                                                <i class="fas fa-infinity me-1"></i> Rolling Deadline
                                            </span>
                                        @endif
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        <span class="status-badge {{ $scholarship->is_active ? 'status-active' : 'status-inactive' }}">
                                            <i class="fas {{ $scholarship->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                            {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.scholarships.show', $scholarship->id) }}" 
                                               class="btn btn-action btn-view" 
                                               title="View Scholarship">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" 
                                               class="btn btn-action btn-edit" 
                                               title="Edit Scholarship">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-action btn-delete delete-scholarship-btn" 
                                                    data-id="{{ $scholarship->id }}"
                                                    data-title="{{ $scholarship->title }}"
                                                    title="Delete Scholarship">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                        <h5 class="text-muted">No Scholarships Found</h5>
                                        <p class="text-muted">Get started by adding your first scholarship.</p>
                                        <a href="{{ route('admin.scholarships.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus me-2"></i> Add New Scholarship
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if($scholarships->hasPages())
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <small class="text-muted">
                                        Showing {{ $scholarships->firstItem() ?? 0 }} to {{ $scholarships->lastItem() ?? 0 }} of {{ $scholarships->total() }} entries
                                    </small>
                                </div>
                                <div>
                                    {{ $scholarships->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
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
                <p id="deleteModalMessage">Are you sure you want to delete this scholarship?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All associated data will be permanently removed.
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
                        <i class="fas fa-trash me-2"></i> Delete Permanently
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
    
    /* Eligibility Badges */
    .eligibility-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .eligibility-maroon {
        background: linear-gradient(135deg, rgba(122,0,25,0.1), rgba(122,0,25,0.05));
        color: #7A0019;
        border: 1px solid rgba(122,0,25,0.2);
    }
    
    .eligibility-gold {
        background: linear-gradient(135deg, rgba(244,197,66,0.15), rgba(244,197,66,0.08));
        color: #92400e;
        border: 1px solid rgba(244,197,66,0.3);
    }
    
    .eligibility-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    
    .eligibility-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }
    
    .eligibility-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
    }
    
    .eligibility-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }
    
    .eligibility-dark {
        background: linear-gradient(135deg, #e5e7eb, #d1d5db);
        color: #374151;
    }
    
    .eligibility-secondary {
        background: #f3f4f6;
        color: #6b7280;
    }
    
    /* Status Badges */
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
    
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        border-radius: 40px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, #4e0010, #7A0019);
    }
    
    .btn-sm {
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
    }
    
    .form-control-sm {
        border-radius: 40px;
        border: 2px solid #e5e7eb;
    }
    
    .form-control-sm:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
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
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }
    
    .btn-danger {
        background: linear-gradient(115deg, #dc2626, #b91c1c);
        border: none;
        border-radius: 40px;
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
    
    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background: white;
    }
    
    @media (max-width: 768px) {
        .table th, .table td {
            padding: 10px;
            font-size: 0.8rem;
        }
        
        .eligibility-badge {
            font-size: 0.65rem;
            padding: 3px 8px;
        }
        
        .btn-action {
            width: 28px;
            height: 28px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.delete-scholarship-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('deleteForm').action = `/admin/scholarships/${id}`;
            document.getElementById('deleteModalMessage').innerHTML = `Are you sure you want to delete <strong>"${title}"</strong>?`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#scholarshipsTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush

@endsection