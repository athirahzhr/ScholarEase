@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Manual Scraper Control</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.scraper.run') }}">
        @csrf

        <div class="mb-3">
            <label>Select Scraper</label>
            <select name="command" class="form-control">
                @foreach($commands as $cmd)
                    <option value="{{ $cmd }}">{{ $cmd }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Run Scraper
        </button>
    </form>
</div>
@endsection