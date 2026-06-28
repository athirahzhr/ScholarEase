@extends('layouts.admin')

@section('title', 'Scholarship Resource Centre')

@section('content')

<style>
    /* ============================================ */
    /* CARD STYLES                                 */
    /* ============================================ */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 2px solid rgba(244, 197, 66, 0.3);
        background: rgba(122, 0, 25, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h5,
    .card-header h6 {
        color: #7A0019;
        font-weight: 700;
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    /* ============================================ */
    /* PAGE HEADER                                 */
    /* ============================================ */
    .text-maroon {
        color: #7A0019;
    }

    /* ============================================ */
    /* TABLE STYLES                                */
    /* ============================================ */
    .table {
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 0;
    }

    .table thead th {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        color: #7A0019;
        font-weight: 700;
        border-bottom: 2px solid #F4C542;
        padding: 14px 18px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.9rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: rgba(122, 0, 25, 0.02);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ============================================ */
    /* BADGE STYLES                                */
    /* ============================================ */
    .badge-active {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        padding: 5px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-inactive {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        padding: 5px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-category {
        background: rgba(122, 0, 25, 0.08);
        color: #7A0019;
        padding: 5px 14px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* ============================================ */
    /* MAROON ACTION BUTTONS                       */
    /* ============================================ */
    .action-buttons {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        align-items: center;
    }

    .btn-maroon-view,
    .btn-maroon-edit,
    .btn-maroon-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 0.3rem 0.8rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.7rem;
        transition: all 0.3s ease;
        border: none;
        color: white;
        text-decoration: none;
        white-space: nowrap;
        min-width: 60px;
    }

    .btn-maroon-view {
        background: linear-gradient(115deg, #7A0019, #4e0010);
    }

    .btn-maroon-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
        color: white;
        background: linear-gradient(115deg, #4e0010, #7A0019);
    }

    .btn-maroon-edit {
        background: linear-gradient(115deg, #7A0019, #4e0010);
    }

    .btn-maroon-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
        color: white;
        background: linear-gradient(115deg, #4e0010, #7A0019);
    }

    .btn-maroon-delete {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        cursor: pointer;
    }

    .btn-maroon-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
        color: white;
        background: linear-gradient(115deg, #4e0010, #7A0019);
    }

    /* ============================================ */
    /* PRIMARY BUTTON                              */
    /* ============================================ */
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.25);
        background: linear-gradient(115deg, #4e0010, #7A0019);
        color: white;
    }

    /* ============================================ */
    /* EMPTY STATE                                 */
    /* ============================================ */
    .empty-state {
        text-align: center;
        padding: 4rem 1.5rem;
    }

    .empty-state .empty-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
        display: block;
        opacity: 0.6;
    }

    .empty-state h5 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .empty-state p {
        color: #6b7280;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    /* ============================================ */
    /* PAGINATION                                  */
    /* ============================================ */
    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }

    .pagination .page-link {
        border: none;
        border-radius: 8px;
        color: #6b7280;
        font-weight: 500;
        padding: 0.5rem 0.9rem;
        transition: all 0.2s ease;
        background: transparent;
        font-size: 0.85rem;
    }

    .pagination .page-link:hover {
        background: rgba(122, 0, 25, 0.06);
        color: #7A0019;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        color: white;
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.15);
    }

    .pagination .page-item.disabled .page-link {
        color: #d1d5db;
        background: transparent;
    }

    /* ============================================ */
    /* STATS MINI CARD                             */
    /* ============================================ */
    .stats-mini {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        padding: 12px 0 4px;
    }

    .stats-mini-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .stats-mini-item .num {
        font-weight: 700;
        color: #7A0019;
        font-size: 1rem;
    }

    /* ============================================ */
    /* RESPONSIVE                                  */
    /* ============================================ */
    @media (max-width: 992px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
            padding: 16px 20px;
        }
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
        }

        .d-flex.justify-content-between.align-items-center .btn {
            width: 100%;
            justify-content: center;
        }

        .card-header {
            padding: 14px 16px;
        }

        .card-body {
            padding: 14px;
        }

        .table thead th,
        .table tbody td {
            padding: 10px 12px;
            font-size: 0.78rem;
        }

        .action-buttons {
            flex-wrap: nowrap;
            gap: 4px;
        }

        .btn-maroon-view,
        .btn-maroon-edit,
        .btn-maroon-delete {
            padding: 0.25rem 0.6rem;
            font-size: 0.6rem;
            min-width: 50px;
        }

        .empty-state {
            padding: 2rem 1rem;
        }

        .empty-state .empty-icon {
            font-size: 3rem;
        }

        .stats-mini {
            gap: 12px;
        }

        .stats-mini-item {
            font-size: 0.75rem;
        }

        .pagination .page-link {
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 10px;
        }

        .table thead th,
        .table tbody td {
            padding: 8px 10px;
            font-size: 0.7rem;
        }

        .action-buttons {
            flex-wrap: nowrap;
            gap: 3px;
        }

        .btn-maroon-view,
        .btn-maroon-edit,
        .btn-maroon-delete {
            padding: 0.15rem 0.5rem;
            font-size: 0.55rem;
            min-width: 40px;
        }

        .btn-maroon-view i,
        .btn-maroon-edit i,
        .btn-maroon-delete i {
            font-size: 0.5rem;
        }

        .badge-active,
        .badge-inactive,
        .badge-category {
            font-size: 0.65rem;
            padding: 3px 10px;
        }

        .stats-mini-item {
            font-size: 0.7rem;
        }
    }
</style>

<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold text-maroon">
                <i class="fas fa-video me-2"></i>
                Scholarship Resource Centre
            </h4>
            <p class="text-muted mb-0 mt-1">Manage video resources for students</p>
        </div>
        <a href="{{ route('admin.resource-videos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>
            Add Video
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- ALERT REMOVED - Layout handles this         --}}
    {{-- ============================================ --}}

    {{-- Main Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>
                All Videos
            </h6>
            <div class="stats-mini">
                <span class="stats-mini-item">
                    <i class="fas fa-video text-maroon"></i>
                    Total: <span class="num">{{ $videos->total() }}</span>
                </span>
                <span class="stats-mini-item">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    Active: <span class="num">{{ $videos->where('is_active', true)->count() }}</span>
                </span>
                <span class="stats-mini-item">
                    <i class="fas fa-circle" style="color: #6b7280;"></i>
                    Inactive: <span class="num">{{ $videos->where('is_active', false)->count() }}</span>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Title</th>
                            <th width="160">Category</th>
                            <th width="120">Status</th>
                            <th width="260">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold">{{ $video->title }}</span>
                            </td>
                            <td>
                                <span class="badge-category">
                                    @if($video->category == 'Scholarship Journey')
                                        
                                    @elseif($video->category == 'Scholarship Tips')
                                        
                                    @elseif($video->category == 'Scholarship Interview')
                                        
                                    @endif
                                    {{ $video->category }}
                                </span>
                            </td>
                            <td>
                                @if($video->is_active)
                                    <span class="badge-active">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @else
                                    <span class="badge-inactive">
                                        <i class="fas fa-circle"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ $video->youtube_url }}" 
                                       target="_blank" 
                                       class="btn-maroon-view"
                                       title="View on YouTube">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.resource-videos.edit', $video->id) }}" 
                                       class="btn-maroon-edit"
                                       title="Edit Video">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.resource-videos.destroy', $video->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete this video?')" 
                                                class="btn-maroon-delete"
                                                title="Delete Video">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-video-slash empty-icon"></i>
                                    <h5>No Videos Found</h5>
                                    <p>Start adding video resources to help students with their scholarship journey.</p>
                                    <a href="{{ route('admin.resource-videos.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Add Your First Video
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($videos->hasPages())
                <div class="mt-4">
                    {{ $videos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection