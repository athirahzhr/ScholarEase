@extends('layouts.admin')

@section('title', 'Add Resource Video')

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
        padding: 14px 20px;
        border-bottom: 2px solid rgba(244, 197, 66, 0.3);
        background: rgba(122, 0, 25, 0.04);
    }

    .card-header h5,
    .card-header h6 {
        color: #7A0019;
        font-weight: 700;
        margin: 0;
    }

    .card-body {
        padding: 20px;
    }

    /* ============================================ */
    /* PAGE HEADER                                 */
    /* ============================================ */
    .text-maroon {
        color: #7A0019;
    }

    /* ============================================ */
    /* FORM STYLES                                 */
    /* ============================================ */
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-label .text-danger {
        color: #dc2626 !important;
        margin-left: 2px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.15);
        outline: none;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
    }

    .invalid-feedback {
        font-size: 0.8rem;
        color: #dc2626;
        margin-top: 4px;
    }

    .text-muted {
        color: #6b7280 !important;
        font-size: 0.78rem;
    }

    /* ============================================ */
    /* BUTTON STYLES                               */
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

    .btn-outline-secondary {
        border: 2px solid #d1d5db;
        color: #6b7280;
        background: transparent;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-outline-secondary:hover {
        background: #6b7280;
        color: white;
        border-color: #6b7280;
        transform: translateY(-2px);
    }

    .btn-success {
        background: linear-gradient(115deg, #10b981, #059669);
        border: none;
        padding: 0.65rem 1.5rem;
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

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
        background: linear-gradient(115deg, #059669, #047857);
        color: white;
    }

    /* ============================================ */
    /* RESPONSIVE                                  */
    /* ============================================ */
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
            padding: 12px 16px;
        }

        .card-body {
            padding: 16px;
        }

        .form-control,
        .form-select {
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .btn-primary,
        .btn-outline-secondary,
        .btn-success {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding: 0 8px !important;
        }

        .card-body {
            padding: 12px;
        }

        .form-check {
            padding-left: 1.6rem;
        }

        .form-check-input {
            width: 1rem;
            height: 1rem;
            margin-left: -1.6rem;
        }

        .form-check-label {
            font-size: 0.82rem;
        }
    }
</style>

<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold text-maroon">
                <i class="fas fa-video me-2"></i>
                Add Resource Video
            </h4>
            <p class="text-muted mb-0 mt-1">Add a new video resource for students</p>
        </div>
        <a href="{{ route('admin.resource-videos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Back to List
        </a>
    </div>

    {{-- Main Card --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Video Details
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.resource-videos.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            {{-- LEFT COLUMN --}}
                            <div class="col-lg-8">
                                {{-- TITLE --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Video Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title') }}" 
                                           placeholder="e.g., How to Apply for JPA Scholarship" 
                                           required>
                                    <small class="text-muted">Give your video a clear and descriptive title</small>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- YOUTUBE URL --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        YouTube URL <span class="text-danger">*</span>
                                    </label>
                                    <input type="url" 
                                           name="youtube_url" 
                                           class="form-control @error('youtube_url') is-invalid @enderror" 
                                           value="{{ old('youtube_url') }}" 
                                           placeholder="https://www.youtube.com/watch?v=..." 
                                           required>
                                    <small class="text-muted">Paste the full YouTube video URL</small>
                                    @error('youtube_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- DESCRIPTION --}}
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">
                                        Description 
                                        <span class="text-muted">(Optional)</span>
                                    </label>
                                    <textarea name="description" 
                                              rows="4" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              placeholder="Provide a brief description of the video content...">{{ old('description') }}</textarea>
                                    <small class="text-muted">Brief description of what the video covers</small>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- RIGHT COLUMN --}}
                            <div class="col-lg-4">
                                {{-- CATEGORY --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select name="category" 
                                            class="form-select @error('category') is-invalid @enderror" 
                                            required>
                                        <option value="">Select Category</option>
                                        <option value="Scholarship Journey" {{ old('category') == 'Scholarship Journey' ? 'selected' : '' }}>
                                            Scholarship Journey
                                        </option>
                                        <option value="Scholarship Tips" {{ old('category') == 'Scholarship Tips' ? 'selected' : '' }}>
                                            Scholarship Tips
                                        </option>
                                        <option value="Scholarship Interview" {{ old('category') == 'Scholarship Interview' ? 'selected' : '' }}>
                                            Scholarship Interview
                                        </option>
                                    </select>
                                    <small class="text-muted">Select the most relevant category</small>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- STATUS --}}
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">
                                        Status
                                    </label>
                                    <select name="is_active" class="form-select">
                                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>
                                            🟢 Active - Visible to Students
                                        </option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                            🔴 Inactive - Hidden
                                        </option>
                                    </select>
                                    <small class="text-muted">Inactive videos won't appear on the student dashboard</small>
                                </div>
                            </div>
                        </div>

                        {{-- FORM ACTIONS --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm" style="background: rgba(122, 0, 25, 0.02);">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                            <a href="{{ route('admin.resource-videos.index') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>
                                                Cancel
                                            </a>
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-save me-2"></i>
                                                Save Video
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection