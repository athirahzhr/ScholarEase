@extends('layouts.admin')

@section('title', 'Notification Center')

@section('content')

<!-- STATS CARDS -->
<div class="row mb-4">

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h3>{{ $totalBookmarks }}</h3>
            <p>Total Bookmarks</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center text-warning">
            <h3>{{ $pending }}</h3>
            <p>Pending</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center text-success">
            <h3>{{ $sent }}</h3>
            <p>Success</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center text-danger">
            <h3>{{ $failed }}</h3>
            <p>Failed</p>
        </div>
    </div>

</div>

<!-- ACTION BUTTONS -->
<div class="mb-4 d-flex gap-2">

    <form method="POST" action="{{ route('admin.notifications.all') }}">
        @csrf
        <button class="btn btn-warning">
            Send All Pending
        </button>
    </form>

</div>

<!-- TABS -->
<ul class="nav nav-tabs mb-3">

    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#pending">
            Pending
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#history">
            History
        </a>
    </li>

</ul>

<div class="tab-content">

    <!-- PENDING TAB -->
    <div class="tab-pane fade show active" id="pending">

        <div class="card">
            <div class="card-body">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Scholarship</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pendingList as $item)
                        <tr>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->scholarship->title }}</td>
                            <td>{{ $item->scholarship->deadline }}</td>

                            <td>
                                <span class="badge bg-warning">
                                    Pending
                                </span>
                            </td>

                            <td>
                                <form method="POST" action="{{ route('admin.notifications.single') }}">
                                    @csrf
                                    <input type="hidden" name="bookmark_id" value="{{ $item->id }}">

                                    <button class="btn btn-sm btn-primary">
                                        Send
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No pending notifications
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>

    </div>

    <!-- HISTORY TAB -->
    <div class="tab-pane fade" id="history">

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Type</th>
                            <th>Data</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($history as $item)
                        <tr>
                            <td>{{ $item->notifiable_id }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($item->data, 80) }}</td>
                            <td>{{ $item->created_at }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                No history
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>

    </div>

</div>

@endsection