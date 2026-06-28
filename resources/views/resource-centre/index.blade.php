@extends('layouts.app')

@section('title', 'Scholarship Resource Centre')

@section('content')

<style>
    :root {
        --maroon: #7A0019;
        --maroon-dark: #4e0010;
        --gold: #F4C542;
        --cream: #FFF8EE;
        --cream-dark: #f5ebe0;
        --gray-800: #1f2937;
        --gray-600: #4b5563;
    }

    .resource-header {
        background: linear-gradient(135deg, var(--cream), var(--cream-dark));
        padding: 2.5rem 2rem;
        border-radius: 24px;
        margin-bottom: 2.5rem;
        border-left: 4px solid var(--gold);
        text-align: center;
    }

    .resource-header h2 {
        color: var(--maroon);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .resource-header p {
        color: var(--gray-600);
        margin-bottom: 0;
        font-size: 1.05rem;
    }

    .section-title {
        color: var(--maroon);
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--gold);
        display: inline-block;
    }

    .video-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid rgba(122, 0, 25, 0.06);
    }

    .video-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        border-color: var(--gold);
    }

    .video-card .card-img-top {
        width: 100%;
        aspect-ratio: 16/9;
        object-fit: cover;
        border-bottom: 3px solid var(--gold);
    }

    .video-card .card-body {
        padding: 1.25rem 1.25rem 0.75rem;
    }

    .video-card .card-body h5 {
        color: var(--maroon);
        font-weight: 700;
        font-size: 1.05rem;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 3rem;
    }

    .video-card .card-body p {
        color: var(--gray-600);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 4rem;
    }

    .video-card .card-footer {
        background: white;
        border-top: none;
        padding: 0 1.25rem 1.25rem;
    }

    .btn-watch {
        background: linear-gradient(115deg, #dc2626, #b91c1c);
        border: none;
        border-radius: 40px;
        padding: 0.6rem 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        text-decoration: none;
    }

    .btn-watch:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        color: white;
        background: linear-gradient(115deg, #b91c1c, #991b1b);
    }

    .btn-watch i {
        font-size: 0.9rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        background: white;
        border-radius: 20px;
        border: 2px dashed #e5e7eb;
        width: 100%;
    }

    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
        display: block;
    }

    .empty-state h5 {
        color: var(--gray-800);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gray-600);
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .resource-header {
            padding: 1.5rem 1rem;
        }

        .resource-header h2 {
            font-size: 1.5rem;
        }

        .video-card .card-body h5 {
            font-size: 0.95rem;
            min-height: 2.5rem;
        }

        .video-card .card-body p {
            font-size: 0.85rem;
            min-height: 3.5rem;
        }

        .section-title {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 576px) {
        .resource-header {
            padding: 1rem;
        }

        .resource-header h2 {
            font-size: 1.3rem;
        }

        .resource-header p {
            font-size: 0.9rem;
        }

        .video-card .card-body {
            padding: 1rem 1rem 0.5rem;
        }

        .video-card .card-footer {
            padding: 0 1rem 1rem;
        }

        .btn-watch {
            font-size: 0.8rem;
            padding: 0.5rem 0.8rem;
        }
    }
</style>

<div class="container py-5">

    {{-- Page Header --}}
    <div class="resource-header" data-aos="fade-up">
        <h2>
            <i class="fas fa-video me-2" style="color: var(--gold);"></i>
            Scholarship Resource Centre
        </h2>
        <p>Watch curated educational videos to help you prepare for scholarship applications.</p>
    </div>

    {{-- ============================================ --}}
    {{-- SCHOLARSHIP JOURNEY                         --}}
    {{-- ============================================ --}}
    <div data-aos="fade-up" data-aos-delay="100">
        <h3 class="section-title">
            <i class="fas fa-graduation-cap me-2" style="color: var(--gold);"></i>
            Scholarship Journey
        </h3>

        <div class="row">
            @forelse($journey as $video)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="video-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                        <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/hqdefault.jpg" 
                             class="card-img-top" 
                             alt="{{ $video->title }}">
                        <div class="card-body">
                            <h5>{{ $video->title }}</h5>
                            <p>{{ Str::limit($video->description ?? 'No description available.', 100) }}</p>
                        </div>
                        <div class="card-footer">
                            <button
                            type="button"
                            class="btn btn-danger w-100"

                            data-bs-toggle="modal"
                            data-bs-target="#videoModal"

                            data-video="{{ $video->youtube_id }}"
                            data-title="{{ $video->title }}"
                            data-description="{{ $video->description }}">

                            <i class="fas fa-play-circle me-2"></i>

                            Watch Video

                        </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-video-slash"></i>
                        <h5>No Videos Available</h5>
                        <p>Check back later for new scholarship journey videos.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SCHOLARSHIP TIPS                            --}}
    {{-- ============================================ --}}
    <div data-aos="fade-up" data-aos-delay="200">
        <h3 class="section-title">
            <i class="fas fa-lightbulb me-2" style="color: var(--gold);"></i>
            Scholarship Tips
        </h3>

        <div class="row">
            @forelse($tips as $video)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="video-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                        <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/hqdefault.jpg" 
                             class="card-img-top" 
                             alt="{{ $video->title }}">
                        <div class="card-body">
                            <h5>{{ $video->title }}</h5>
                            <p>{{ Str::limit($video->description ?? 'No description available.', 100) }}</p>
                        </div>
                        <div class="card-footer">
                            <button
                            type="button"
                            class="btn btn-danger w-100"

                            data-bs-toggle="modal"
                            data-bs-target="#videoModal"

                            data-video="{{ $video->youtube_id }}"
                            data-title="{{ $video->title }}"
                            data-description="{{ $video->description }}">

                            <i class="fas fa-play-circle me-2"></i>

                            Watch Video

                        </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-video-slash"></i>
                        <h5>No Videos Available</h5>
                        <p>Check back later for new scholarship tips.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SCHOLARSHIP INTERVIEW                       --}}
    {{-- ============================================ --}}
    <div data-aos="fade-up" data-aos-delay="300">
        <h3 class="section-title">
            <i class="fas fa-microphone me-2" style="color: var(--gold);"></i>
            Scholarship Interview
        </h3>

        <div class="row">
            @forelse($interview as $video)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="video-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                        <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/hqdefault.jpg" 
                             class="card-img-top" 
                             alt="{{ $video->title }}">
                        <div class="card-body">
                            <h5>{{ $video->title }}</h5>
                            <p>{{ Str::limit($video->description ?? 'No description available.', 100) }}</p>
                        </div>
                        <div class="card-footer">
                           <button
                            type="button"
                            class="btn btn-danger w-100"

                            data-bs-toggle="modal"
                            data-bs-target="#videoModal"

                            data-video="{{ $video->youtube_id }}"
                            data-title="{{ $video->title }}"
                            data-description="{{ $video->description }}">

                            <i class="fas fa-play-circle me-2"></i>

                            Watch Video

                        </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-video-slash"></i>
                        <h5>No Videos Available</h5>
                        <p>Check back later for interview preparation videos.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
@endpush

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="videoTitle">
                    Video
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="ratio ratio-16x9">

                    <iframe
                        id="youtubePlayer"
                        src=""
                        frameborder="0"
                        allow="autoplay; encrypted-media"
                        allowfullscreen>

                    </iframe>

                </div>

                <div class="mt-3">

                    <p id="videoDescription"></p>

                </div>

            </div>

        </div>

    </div>

</div>
@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    const videoModal = document.getElementById('videoModal');

    if (!videoModal) return;

    videoModal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        const youtubeId = button.getAttribute('data-video');
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');

        document.getElementById('videoTitle').textContent = title;
        document.getElementById('videoDescription').textContent = description;

        document.getElementById('youtubePlayer').src =
            "https://www.youtube.com/embed/" +
            youtubeId +
            "?autoplay=1&rel=0";

    });

    videoModal.addEventListener('hidden.bs.modal', function () {

        document.getElementById('youtubePlayer').src = "";

    });

});

</script>
@endpush
@endsection