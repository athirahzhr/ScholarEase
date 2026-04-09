@extends('layouts.admin')

@section('title', 'Scraping Management')

@section('content')
<div class="container-fluid">

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Scholarships</h6>
                    <h2>{{ $totalScholarships }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Active Scholarships</h6>
                    <h2>{{ $activeScholarships }}</h2>
                </div>
            </div>
        </div>

    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history me-2"></i> Scraping Logs</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Website</th>
                        <th>Status</th>
                        <th>Scholarships Added</th>
                        <th>Duration (s)</th>
                        <th>Executed At</th>
                    </tr>
                </thead>
               <tbody>
@forelse($logs as $log)
<tr>
    <td>{{ $loop->iteration }}</td>

    {{-- Website --}}
    <td>{{ strtoupper($log->source_website) }}</td>

    {{-- Status --}}
    <td>
        <span class="badge {{ $log->status === 'success' ? 'bg-success' : 'bg-danger' }}">
            {{ ucfirst($log->status) }}
        </span>
    </td>

    {{-- Scholarships Added --}}
    <td>{{ $log->success_count }}</td>

    {{-- Duration --}}
    <td>
        @if($log->started_at && $log->finished_at)
            {{ \Carbon\Carbon::parse($log->started_at)
                ->diffInSeconds(\Carbon\Carbon::parse($log->finished_at)) }} s
        @else
            -
        @endif
    </td>

    {{-- Executed At --}}
    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">
        No scraping logs found
    </td>
</tr>
@endforelse
</tbody>

            </table>

            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
