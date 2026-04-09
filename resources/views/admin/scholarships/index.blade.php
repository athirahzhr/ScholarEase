@extends('layouts.admin')

@section('title', 'Manage Scholarships')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i> Manage Scholarships
                    </h5>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.scholarships.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add New
                        </a>
                        <input type="text" id="searchInput"
                               class="form-control form-control-sm w-auto"
                               placeholder="Search...">
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="scholarshipsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Provider</th>
                                    <th>Eligibility </th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                            @forelse($scholarships as $scholarship)
                                <tr>
                                    <td>
                                        {{ ($scholarships->currentPage() - 1) * $scholarships->perPage() + $loop->iteration }}
                                    </td>

                                    <td>
                                        <strong>{{ Str::limit($scholarship->title, 40) }}</strong>
                                        <div class="text-muted small">ID: {{ $scholarship->id }}</div>
                                    </td>

                                    <td>{{ $scholarship->provider }}</td>

                                    {{-- ELIGIBILITY SUMMARY (RULE-BASED) --}}
                                    <td>
                                        @php
                                            $c = $scholarship->eligibilityCriteria;
                                        @endphp

                                        @if($c)
                                            {{-- Academic --}}
                                            @if($c->min_spm_as)
                                                <span class="badge bg-primary mb-1">
                                                    ≥ {{ $c->min_spm_as }} A’s
                                                </span>
                                            @endif

                                            {{-- Income --}}
                                            @php
                                                $incomeCategories = is_array($c->income_categories)
                                                    ? $c->income_categories
                                                    : json_decode($c->income_categories ?? '[]', true);
                                            @endphp

                                            @foreach($incomeCategories as $inc)
                                                <span class="badge bg-success mb-1">
                                                    {{ $inc === 'B1' ? 'B40' : $inc }}
                                                </span>
                                            @endforeach


                                            {{-- Study Path --}}
                                           @php
                                            $studyPaths = is_array($c->study_paths)
                                                ? $c->study_paths
                                                : json_decode($c->study_paths ?? '[]', true);
                                        @endphp

                                        @foreach($studyPaths as $path)
                                            <span class="badge bg-warning text-dark mb-1">
                                                {{ match($path) {
                                                    'C1' => 'Pre-U',
                                                    'C2' => 'Diploma',
                                                    'C3' => 'Degree',
                                                    'C4' => 'TVET',
                                                    default => $path
                                                } }}
                                            </span>
                                        @endforeach


                                            {{-- Bumiputera --}}
                                            @if($c->bumiputera_required)
                                                <span class="badge bg-danger mb-1">
                                                    Bumiputera Only
                                                </span>
                                            @elseif($c->bumiputera_priority)
                                                <span class="badge bg-secondary mb-1">
                                                    Bumiputera Priority
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                Not inferred
                                            </span>
                                        @endif
                                    </td>

                                  

                                    {{-- Deadline --}}
                                    <td>
                                        @if($scholarship->deadline)
                                            {{ $scholarship->deadline->format('d M Y') }}
                                            <div class="small {{ $scholarship->deadline->isPast() ? 'text-danger' : 'text-success' }}">
                                                {{ $scholarship->deadline->isPast()
                                                    ? 'Expired'
                                                    : $scholarship->deadline->diffForHumans() }}
                                            </div>
                                        @else
                                            <span class="text-muted">No deadline</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="badge {{ $scholarship->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.scholarships.show', $scholarship->id) }}"
                                               class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                        

                                            <button type="button"
                                                    class="btn btn-outline-danger delete-scholarship-btn"
                                                    data-id="{{ $scholarship->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No scholarships found
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $scholarships->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete this scholarship?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </div>
            </div>

            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Delete
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-scholarship-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        document.getElementById('deleteForm').action =
            `/admin/scholarships/${id}`;

        new bootstrap.Modal(
            document.getElementById('deleteModal')
        ).show();
    });
});
</script>
@endpush
