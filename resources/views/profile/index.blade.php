@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<style>
    :root {
        --maroon: #7A0019;
        --maroon-dark: #4e0010;
        --maroon-light: #9e1e32;
        --gold: #F4C542;
        --gold-light: #ffda77;
        --cream: #FFF8EE;
        --cream-dark: #f5ebe0;
        --gray-800: #1f2937;
        --gray-600: #4b5563;
    }

    .profile-container {
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    .profile-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 1.5rem 2rem;
        border-bottom: 3px solid var(--gold);
    }

    .profile-header h2 {
        color: white;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
    }

    .profile-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0;
        font-size: 0.9rem;
    }

    .profile-body {
        padding: 2rem;
    }

    .edit-btn {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        color: white;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }

    .edit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        color: white;
    }

    .create-btn {
        background: linear-gradient(115deg, #10b981, #059669);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 40px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .info-card {
        border: none;
        border-radius: 20px;
        background: white;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }

    .info-card-header {
        padding: 1rem 1.5rem;
        font-weight: 700;
        border-bottom: 2px solid;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-card-body {
        padding: 1.5rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--gray-800);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-label i {
        width: 20px;
        color: var(--maroon);
    }

    .info-value {
        color: var(--gray-600);
        font-weight: 500;
    }

    .info-value .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-yes {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .badge-no {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
        display: block;
    }

    .empty-state h5 {
        color: var(--gray-800);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gray-600);
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 1.2rem 1.5rem;
        }
        
        .profile-header h2 {
            font-size: 1.3rem;
        }
        
        .profile-body {
            padding: 1.5rem;
        }
        
        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .edit-btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="profile-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="profile-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h2>
                                    <i class="fas fa-user-circle me-2"></i>
                                    Your Profile
                                </h2>
                                <p>View and manage your personal and academic information</p>
                            </div>
                            @if($profile)
                                <a href="{{ route('profile.create') }}" class="edit-btn">
                                    <i class="fas fa-edit"></i>
                                    Edit Profile
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="profile-body">
                        @if($profile)
                            <div class="row g-4">
                                <!-- Academic Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-card-header" style="border-bottom-color: var(--maroon);">
                                            <i class="fas fa-graduation-cap" style="color: var(--maroon); font-size: 1.2rem;"></i>
                                            <span style="color: var(--maroon);">Academic Information</span>
                                        </div>
                                        <div class="info-card-body">
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-star"></i>
                                                    Total A's (SPM)
                                                </div>
                                                <div class="info-value">
                                                    <span class="badge" style="background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); color: white;">
                                                        {{ $profile->total_as ?? 'Not Set' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-road"></i>
                                                    Study Level
                                                </div>
                                                <div class="info-value">
                                                    {{ $profile->study_level ?? 'Not Set' }}
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-book"></i>
                                                    Field of Study
                                                </div>
                                                <div class="info-value">
                                                    {{ $profile->field_of_study ?? 'Not Set' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-card-header" style="border-bottom-color: #10b981;">
                                            <i class="fas fa-money-bill-wave" style="color: #10b981; font-size: 1.2rem;"></i>
                                            <span style="color: #10b981;">Financial Information</span>
                                        </div>
                                        <div class="info-card-body">
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-chart-line"></i>
                                                    Income Category
                                                </div>
                                                <div class="info-value">
                                                    <span class="badge" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af;">
                                                        {{ $profile->income_category ?? 'Not Set' }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if($profile->monthly_income)
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-ring"></i>
                                                    Monthly Income
                                                </div>
                                                <div class="info-value">
                                                    RM {{ number_format($profile->monthly_income, 2) }}
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-card-header" style="border-bottom-color: #f59e0b;">
                                            <i class="fas fa-user" style="color: #f59e0b; font-size: 1.2rem;"></i>
                                            <span style="color: #f59e0b;">Personal Information</span>
                                        </div>
                                        <div class="info-card-body">
                                            @if($profile->age)
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-birthday-cake"></i>
                                                    Age
                                                </div>
                                                <div class="info-value">
                                                    {{ $profile->age }} years
                                                </div>
                                            </div>
                                            @endif

                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    State
                                                </div>
                                                <div class="info-value">
                                                    {{ $profile->state ?? 'Not Set' }}
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-passport"></i>
                                                    Citizenship
                                                </div>
                                                <div class="info-value">
                                                    {{ $profile->citizenship ?? 'Not Set' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-card-header" style="border-bottom-color: var(--gold);">
                                            <i class="fas fa-plus-circle" style="color: var(--gold); font-size: 1.2rem;"></i>
                                            <span style="color: #92400e;">Additional Information</span>
                                        </div>
                                        <div class="info-card-body">
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-star-of-life"></i>
                                                    Bumiputera
                                                </div>
                                                <div class="info-value">
                                                    <span class="badge {{ $profile->bumiputera ? 'badge-yes' : 'badge-no' }}">
                                                        {{ $profile->bumiputera ? 'Yes' : 'No' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">
                                                    <i class="fas fa-trophy"></i>
                                                    Leadership Experience
                                                </div>
                                                <div class="info-value">
                                                    <span class="badge {{ $profile->has_leadership ? 'badge-yes' : 'badge-no' }}">
                                                        {{ $profile->has_leadership ? 'Yes' : 'No' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-user-circle"></i>
                                <h5>No Profile Found</h5>
                                <p>Please create your profile to receive personalized scholarship recommendations.</p>
                                <a href="{{ route('profile.create') }}" class="create-btn">
                                    <i class="fas fa-plus-circle"></i>
                                    Create Profile
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
@endpush
@endsection