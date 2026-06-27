@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<style>
    /* ===== ROOT ===== */
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
        --shadow-soft: 0 12px 30px -12px rgba(0,0,0,0.08);
        --radius-card: 28px;
    }

    /* ===== GLOBAL ===== */
    .profile-container {
        background: linear-gradient(145deg, #fcf8f4 0%, #f5efe8 100%);
        min-height: calc(100vh - 200px);
        padding: 2.5rem 0;
    }

    /* ===== MAIN CARD ===== */
    .profile-card {
        background: #ffffff;
        border-radius: var(--radius-card);
        box-shadow: 0 30px 50px -20px rgba(0, 0, 0, 0.12), 0 8px 20px -8px rgba(122, 0, 25, 0.05);
        overflow: hidden;
        border: 1px solid rgba(255, 215, 175, 0.2);
        transition: box-shadow 0.3s ease;
    }

    /* ===== HEADER ===== */
    .profile-header {
        background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
        padding: 1.8rem 2.5rem;
        border-bottom: 4px solid var(--gold);
        position: relative;
    }

    .profile-header::after {
        content: "✦";
        position: absolute;
        right: 2.8rem;
        bottom: 0.8rem;
        font-size: 5.5rem;
        color: rgba(244, 197, 66, 0.07);
        pointer-events: none;
        font-weight: 300;
    }

    .profile-header h2 {
        color: #fff;
        font-weight: 700;
        font-size: 1.7rem;
        letter-spacing: -0.02em;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-header h2 i {
        color: var(--gold);
        filter: drop-shadow(0 2px 6px rgba(244,197,66,0.25));
    }

    .profile-header p {
        color: rgba(255,255,255,0.85);
        margin: 0.3rem 0 0;
        font-size: 0.95rem;
        font-weight: 400;
        letter-spacing: 0.2px;
    }

    /* ===== BUTTONS (preserve original function) ===== */
    .edit-btn {
        background: rgba(255,255,255,0.10);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,215,100,0.25);
        color: #fff;
        padding: 0.65rem 1.8rem;
        border-radius: 60px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.25s ease;
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }

    .edit-btn i {
        color: var(--gold);
    }

    .edit-btn:hover {
        background: var(--gold);
        color: var(--maroon-dark);
        transform: translateY(-2px);
        border-color: var(--gold);
        box-shadow: 0 12px 28px rgba(244,197,66,0.25);
    }

    .edit-btn:hover i {
        color: var(--maroon-dark);
    }

    .create-btn {
        background: linear-gradient(145deg, #10b981, #059669);
        color: #fff;
        padding: 0.8rem 2.4rem;
        border-radius: 60px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.25s ease;
        box-shadow: 0 10px 24px -8px rgba(16,185,129,0.3);
        border: 1px solid rgba(255,255,255,0.15);
    }

    .create-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 36px -10px rgba(16,185,129,0.4);
        color: #fff;
        background: linear-gradient(145deg, #34d399, #059669);
    }

    .create-btn i {
        font-size: 1.1rem;
    }

    /* ===== BODY ===== */
    .profile-body {
        padding: 2.5rem 2.5rem 3rem;
        background: #fcf9f6;
    }

    /* ===== INFO CARDS ===== */
    .info-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
        height: 100%;
        border: 1px solid rgba(230, 215, 200, 0.25);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .info-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 44px -16px rgba(122, 0, 25, 0.12);
        border-color: rgba(244, 197, 66, 0.25);
    }

    .info-card-header {
        padding: 1.1rem 1.6rem;
        font-weight: 700;
        font-size: 1.05rem;
        border-bottom: 2px solid;
        display: flex;
        align-items: center;
        gap: 14px;
        background: rgba(255, 248, 238, 0.25);
        flex-shrink: 0;
    }

    .info-card-header i {
        font-size: 1.3rem;
    }

    .info-card-body {
        padding: 1.6rem 1.6rem 1.8rem;
        flex: 1;
    }

    /* ===== INFO ITEMS ===== */
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.7rem 0;
        border-bottom: 1px solid #f2ede8;
    }

    .info-item:last-of-type {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--gray-800);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.92rem;
    }

    .info-label i {
        width: 22px;
        color: var(--maroon);
        font-size: 0.95rem;
        text-align: center;
    }

    .info-value {
        color: var(--gray-600);
        font-weight: 500;
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
        text-align: right;
    }

    /* ===== BADGES ===== */
    .badge-maroon {
        background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
        color: #fff;
        padding: 0.3rem 1.1rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 10px rgba(122,0,25,0.15);
        display: inline-block;
    }

    .badge-income {
        background: linear-gradient(145deg, #dbeafe, #bfdbfe);
        color: #1e3a6b;
        padding: 0.3rem 1.1rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-yes {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        padding: 0.25rem 1.2rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    .badge-no {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        padding: 0.25rem 1.2rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    /* ===== SPM TABLE ===== */
    .spm-table-wrap {
        margin-top: 0.25rem;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #efe8e0;
        background: #fcf9f6;
    }

    .spm-table-wrap table {
        margin: 0;
        font-size: 0.85rem;
    }

    .spm-table-wrap thead th {
        background: #f5efe8;
        color: var(--gray-800);
        font-weight: 700;
        padding: 0.6rem 0.8rem;
        border-bottom: 2px solid #e2d7cc;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-size: 0.7rem;
    }

    .spm-table-wrap tbody td {
        padding: 0.5rem 0.8rem;
        border-bottom: 1px solid #efe8e0;
        background: #fff;
        vertical-align: middle;
    }

    .spm-table-wrap tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-grade {
        padding: 0.2rem 0.9rem;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.7rem;
        display: inline-block;
        letter-spacing: 0.2px;
        min-width: 38px;
        text-align: center;
    }

    .badge-grade.bg-success-soft {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-grade.bg-primary-soft {
        background: #dbeafe;
        color: #1e3a6b;
    }
    .badge-grade.bg-secondary-soft {
        background: #e5e7eb;
        color: #374151;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 3.5rem 2rem;
        background: rgba(255, 248, 238, 0.3);
        border-radius: 40px;
        backdrop-filter: blur(2px);
        border: 1px dashed rgba(122, 0, 25, 0.12);
    }

    .empty-state i {
        font-size: 4.2rem;
        color: #d1c5b8;
        margin-bottom: 1.2rem;
        display: block;
        opacity: 0.6;
    }

    .empty-state h5 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 0.4rem;
    }

    .empty-state p {
        color: var(--gray-600);
        max-width: 380px;
        margin: 0.3rem auto 1.6rem;
        font-size: 0.95rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .profile-header {
            padding: 1.4rem 1.8rem;
        }
        .profile-header h2 {
            font-size: 1.4rem;
        }
        .profile-body {
            padding: 1.8rem 1.5rem 2rem;
        }
        .info-card-header {
            padding: 0.9rem 1.2rem;
            font-size: 0.95rem;
        }
        .info-card-body {
            padding: 1.2rem 1.2rem 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        .profile-header .d-flex {
            flex-direction: column;
            align-items: stretch;
        }
        .edit-btn {
            width: 100%;
            justify-content: center;
        }
        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.3rem;
            padding: 0.6rem 0;
        }
        .info-value {
            width: 100%;
            justify-content: flex-start;
            text-align: left;
        }
        .profile-body {
            padding: 1.2rem;
        }
        .empty-state {
            padding: 2.5rem 1rem;
        }
        .empty-state i {
            font-size: 3.2rem;
        }
        .empty-state h5 {
            font-size: 1.2rem;
        }
        .spm-table-wrap table {
            font-size: 0.75rem;
        }
        .spm-table-wrap thead th,
        .spm-table-wrap tbody td {
            padding: 0.4rem 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .profile-header h2 {
            font-size: 1.2rem;
        }
        .profile-header p {
            font-size: 0.8rem;
        }
        .info-card-header {
            font-size: 0.85rem;
            padding: 0.7rem 1rem;
        }
        .info-label {
            font-size: 0.82rem;
            gap: 8px;
        }
        .info-value {
            font-size: 0.82rem;
        }
        .badge-maroon, .badge-income, .badge-yes, .badge-no {
            font-size: 0.65rem;
            padding: 0.2rem 0.8rem;
        }
    }
</style>

<div class="profile-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-card" data-aos="fade-up" data-aos-duration="700">

                    <!-- ===== HEADER ===== -->
                    <div class="profile-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
                            <div>
                                <h2>
                                    <i class="fas fa-user-astronaut"></i>
                                    Your Profile
                                </h2>
                                <p><i class="fas fa-sparkles" style="color: var(--gold); margin-right:6px;"></i>View &amp; manage your academic and personal data</p>
                            </div>
                            @if($profile)
                                <a href="{{ route('profile.create') }}" class="edit-btn">
                                    <i class="fas fa-pen-fancy"></i>
                                    Edit Profile
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- ===== BODY ===== -->
                    <div class="profile-body">

                        @if($profile)
                            <div class="row g-4">

                                <!-- ===== ACADEMIC CARD ===== -->
                                <div class="col-md-6">
                                    <div class="info-card" data-aos="fade-up" data-aos-delay="60">
                                        <div class="info-card-header" style="border-bottom-color: var(--maroon);">
                                            <i class="fas fa-graduation-cap" style="color: var(--maroon);"></i>
                                            <span style="color: var(--maroon);">Academic</span>
                                        </div>
                                        <div class="info-card-body">
                                            <div class="info-item">
                                                <span class="info-label"><i class="fas fa-star"></i> Total A's (SPM)</span>
                                                <span class="info-value">
                                                    <span class="badge-maroon">{{ $profile->total_as ?? 'Not Set' }}</span>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label"><i class="fas fa-layer-group"></i> Study Level</span>
                                                <span class="info-value">{{ $profile->study_level ?? 'Not Set' }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label"><i class="fas fa-flask"></i> Field of Study</span>
                                                <span class="info-value">{{ $profile->field_of_study ?? 'Not Set' }}</span>
                                            </div>

                                            @if(!empty($profile->spm_results))
                                                <hr style="margin: 0.8rem 0 0.6rem; opacity:0.3;">
                                                <div class="mt-2">
                                                    <span class="info-label" style="margin-bottom:0.5rem;"><i class="fas fa-table"></i> SPM Results</span>
                                                    <div class="spm-table-wrap">
                                                        <table class="table table-sm align-middle" style="margin:0;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Subject</th>
                                                                    <th style="text-align:center; width:100px;">Grade</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($profile->spm_results as $subject => $grade)
                                                                    <tr>
                                                                        <td>{{ $subject }}</td>
                                                                        <td style="text-align:center;">
                                                                            @php
                                                                                $gradeClass = in_array($grade, ['A+','A','A-']) ? 'bg-success-soft'
                                                                                            : (in_array($grade, ['B+','B']) ? 'bg-primary-soft' : 'bg-secondary-soft');
                                                                            @endphp
                                                                            <span class="badge-grade {{ $gradeClass }}">{{ $grade }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== FINANCIAL CARD ===== -->
                                <div class="col-md-6">
                                    <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                                        <div class="info-card-header" style="border-bottom-color: #10b981;">
                                            <i class="fas fa-coins" style="color: #10b981;"></i>
                                            <span style="color: #10b981;">Financial</span>
                                        </div>
                                        <div class="info-card-body">
                                            <div class="info-item">
                                                <span class="info-label"><i class="fas fa-chart-pie"></i> Income Category</span>
                                                <span class="info-value">
                                                    <span class="badge-income">{{ $profile->income_category ?? 'Not Set' }}</span>
                                                </span>
                                            </div>
                                            @if($profile->monthly_income)
                                                <div class="info-item">
                                                    <span class="info-label"><i class="fas fa-wallet"></i> Monthly Income</span>
                                                    <span class="info-value" style="font-weight:600; color:#1f2937;">RM {{ number_format($profile->monthly_income, 2) }}</span>
                                                </div>
                                            @endif
                                            <div class="info-item" style="border-bottom:none; padding-top:0.1rem;">
                                                <span class="info-label"><i class="fas fa-credit-card"></i> Eligibility</span>
                                                <span class="info-value"><span style="background:#e6f7e6; padding:0.15rem 1rem; border-radius:30px; font-size:0.75rem; font-weight:600;">Eligible</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== PERSONAL CARD ===== -->
                                <div class="col-md-6">
                                    <div class="info-card" data-aos="fade-up" data-aos-delay="140">
                                        <div class="info-card-header" style="border-bottom-color: #f59e0b;">
                                            <i class="fas fa-id-card" style="color: #f59e0b;"></i>
                                            <span style="color: #b45309;">Personal</span>
                                        </div>
                                        <div class="info-card-body">
                                            @if($profile->age)
                                                <div class="info-item">
                                                    <span class="info-label"><i class="fas fa-cake-candles"></i> Age</span>
                                                    <span class="info-value">{{ $profile->age }} years</span>
                                                </div>
                                            @endif
                                            <div class="info-item">
                                                <span class="info-label"><i class="fas fa-location-dot"></i> State</span>
                                                <span class="info-value">{{ $profile->state ?? 'Not Set' }}</span>
                                            </div>
                                            <div class="info-item" style="border-bottom:none;">
                                                <span class="info-label"><i class="fas fa-passport"></i> Citizenship</span>
                                                <span class="info-value">{{ $profile->citizenship ?? 'Not Set' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== ADDITIONAL CARD ===== -->
                                <div class="col-md-6">
                                    <div class="info-card" data-aos="fade-up" data-aos-delay="180">
                                        <div class="info-card-header" style="border-bottom-color: var(--gold);">
                                            <i class="fas fa-circle-plus" style="color: var(--gold);"></i>
                                            <span style="color: #92400e;">Additional</span>
                                        </div>
                                        <div class="info-card-body">
                                            <div class="info-item">
                                                <span class="info-label"><i class="fas fa-flag"></i> Bumiputera</span>
                                                <span class="info-value">
                                                    <span class="{{ $profile->bumiputera ? 'badge-yes' : 'badge-no' }}">
                                                        {{ $profile->bumiputera ? 'Yes' : 'No' }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="info-item" style="border-bottom:none;">
                                                <span class="info-label"><i class="fas fa-crown"></i> Leadership</span>
                                                <span class="info-value">
                                                    <span class="{{ $profile->has_leadership ? 'badge-yes' : 'badge-no' }}">
                                                        {{ $profile->has_leadership ? 'Yes' : 'No' }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== COMPLETION TIP (extra structure) ===== -->
                                <div class="col-12 mt-2">
                                    <div style="background: linear-gradient(115deg, #fcf3e8, #f8ede2); border-radius: 30px; padding: 1rem 2rem; border-left: 6px solid var(--gold); box-shadow: inset 0 1px 4px rgba(255,255,255,0.7);">
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <i class="fas fa-lightbulb" style="font-size:1.6rem; color: var(--gold);"></i>
                                            <span style="font-weight:500; color:#3d2b1a;">Your profile is <strong style="color: var(--maroon);">92%</strong> complete — add more details for better matches.</span>
                                            <a href="{{ route('profile.create') }}" style="margin-left:auto; background:transparent; border:1px solid var(--maroon); color:var(--maroon); padding:0.2rem 1.8rem; border-radius:40px; text-decoration:none; font-weight:600; transition:0.2s; font-size:0.85rem;">Update</a>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- /row -->

                        @else
                            <!-- ===== EMPTY STATE ===== -->
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

                    </div> <!-- /profile-body -->
                </div> <!-- /profile-card -->
            </div> <!-- /col -->
        </div> <!-- /row -->
    </div> <!-- /container -->
</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });
</script>
@endpush
@endsection