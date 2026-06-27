@extends('layouts.app')

@section('title', 'Find Scholarship')

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

    .finder-container {
        background: linear-gradient(135deg, var(--cream) 0%, var(--cream-dark) 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    .step-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .step-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(122, 0, 25, 0.05);
    }
    
    .step-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gold);
    }
    
    .step-number {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        margin-right: 1rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(122, 0, 25, 0.3);
    }
    
    .step-header h4 {
        color: var(--maroon);
        font-weight: 700;
        margin-bottom: 0.25rem;
        font-size: 1.2rem;
    }
    
    .step-header p {
        color: var(--gray-600);
        margin-bottom: 0;
        font-size: 0.9rem;
    }
    
    /* Progress Bar */
    .progress-custom {
        background: #e5e7eb;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
    }
    
    .progress-bar-custom {
        background: linear-gradient(90deg, var(--maroon), var(--gold));
        width: 33%;
        height: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    
    .step-indicator {
        color: var(--gray-600);
        font-size: 0.85rem;
        padding: 0.25rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .step-indicator i {
        font-size: 1rem;
    }
    
    .step-indicator.active {
        color: var(--maroon);
        font-weight: 700;
    }
    
    .step-indicator.active i {
        color: var(--gold);
    }
    
    /* Upload Area */
    .upload-area {
        border: 2px dashed var(--maroon);
        border-radius: 20px;
        padding: 3rem 2rem;
        text-align: center;
        background: linear-gradient(135deg, #faf5ff, #f3e8ff);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        border-color: var(--gold);
        transform: scale(1.02);
    }
    
    .upload-area.dragover {
        background: #e9d5ff;
        border-color: var(--gold);
        transform: scale(1.02);
    }
    
    .upload-icon {
        font-size: 3.5rem;
        color: var(--maroon);
        margin-bottom: 1rem;
        display: block;
    }
    
    /* Grade Badges */
    .grade-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        margin: 0.15rem;
        font-weight: 700;
        font-size: 0.8rem;
        min-width: 45px;
        text-align: center;
    }
    
    .grade-a-plus { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); color: white; }
    .grade-a { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .grade-a-minus { background: linear-gradient(135deg, #34d399, #10b981); color: white; }
    .grade-b-plus { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
    .grade-b { background: linear-gradient(135deg, #60a5fa, #3b82f6); color: white; }
    .grade-b-minus { background: linear-gradient(135deg, #93c5fd, #60a5fa); color: white; }
    .grade-c-plus { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .grade-c { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
    .grade-other { background: linear-gradient(135deg, #6b7280, #4b5563); color: white; }
    
    /* Buttons - STANDARDIZED */
    .btn {
        border-radius: 40px;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 0.625rem 1.5rem;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 44px;
    }
    
    .btn-primary {
        background: linear-gradient(115deg, var(--maroon), var(--maroon-dark));
        border: none;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, var(--maroon-dark), var(--maroon));
        color: white;
    }
    
    .btn-outline-primary {
        border: 2px solid var(--maroon);
        color: var(--maroon);
        background: transparent;
    }
    
    .btn-outline-primary:hover {
        background: var(--maroon);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-outline-secondary {
        border: 2px solid var(--gray-600);
        color: var(--gray-600);
        background: transparent;
    }
    
    .btn-outline-secondary:hover {
        background: var(--gray-600);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-sm {
        padding: 0.3rem 1rem;
        font-size: 0.8rem;
        min-height: 34px;
        min-width: 60px;
    }
    
    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1.05rem;
        min-height: 52px;
    }
    
    .btn-success {
        background: linear-gradient(115deg, #10b981, #059669);
        border: none;
        color: white;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }
    
    .btn-danger {
        background: linear-gradient(115deg, #ef4444, #dc2626);
        border: none;
        color: white;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        color: white;
    }
    
    .btn-block {
        width: 100%;
    }
    
    /* Button Groups */
    .button-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }
    
    .button-group-center {
        justify-content: center;
    }
    
    .button-group-between {
        justify-content: space-between;
    }
    
    .button-group-end {
        justify-content: flex-end;
    }
    
    /* Tables */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        background: linear-gradient(135deg, var(--cream), var(--cream-dark));
        color: var(--maroon);
        font-weight: 700;
        border-bottom: 2px solid var(--gold);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .table td {
        vertical-align: middle;
        padding: 0.75rem;
    }
    
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .table-hover tbody tr:hover {
        background: rgba(122, 0, 25, 0.03);
    }
    
    /* Verified Grades */
    .verified-grades {
        border: 2px solid #10b981;
        border-radius: 16px;
        padding: 1.5rem;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    }
    
    /* Form Controls */
    .form-control, .form-select {
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        padding: 0.6rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
        outline: none;
    }
    
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--gray-800);
    }
    
    /* Alerts */
    .alert {
        border-radius: 12px;
        border: none;
    }
    
    .alert-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-left: 4px solid #3b82f6;
        color: #1e40af;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-left: 4px solid #f59e0b;
        color: #92400e;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border-left: 4px solid #10b981;
        color: #065f46;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border-left: 4px solid #ef4444;
        color: #991b1b;
    }
    
    .text-maroon {
        color: var(--maroon);
    }
    
    .bg-maroon-soft {
        background: rgba(122, 0, 25, 0.05);
    }
    
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 1rem; }
    .gap-4 { gap: 1.5rem; }
    
    .flex-1 { flex: 1; }
    
    .mt-3 { margin-top: 1rem; }
    .mt-4 { margin-top: 1.5rem; }
    .mt-5 { margin-top: 2rem; }
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mb-5 { margin-bottom: 2rem; }
    
    .d-flex { display: flex; }
    .flex-column { flex-direction: column; }
    .align-items-center { align-items: center; }
    .justify-content-between { justify-content: space-between; }
    .justify-content-center { justify-content: center; }
    .justify-content-end { justify-content: flex-end; }
    
    .text-center { text-align: center; }
    .text-end { text-align: right; }
    
    .w-100 { width: 100%; }
    
    .d-none { display: none !important; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .step-card {
            padding: 1.25rem;
        }
        
        .upload-area {
            padding: 1.5rem 1rem;
        }
        
        .upload-icon {
            font-size: 2.5rem;
        }
        
        .table {
            font-size: 0.8rem;
        }
        
        .table td, .table th {
            padding: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            min-height: 40px;
        }
        
        .btn-lg {
            padding: 0.6rem 1.25rem;
            font-size: 0.95rem;
            min-height: 46px;
        }
        
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            min-height: 30px;
        }
        
        .step-number {
            width: 38px;
            height: 38px;
            font-size: 1rem;
        }
        
        .step-header h4 {
            font-size: 1rem;
        }
        
        .step-header p {
            font-size: 0.8rem;
        }
        
        .step-indicator {
            font-size: 0.7rem;
        }
        
        .step-indicator i {
            display: none;
        }
        
        .button-group {
            flex-direction: column;
            width: 100%;
        }
        
        .button-group .btn {
            width: 100%;
        }
        
        .button-group-between {
            flex-direction: column-reverse;
        }
        
        .row .col-md-4, .row .col-md-6, .row .col-md-8 {
            margin-bottom: 0.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .step-card {
            padding: 1rem;
        }
        
        .step-header {
            flex-direction: column;
            text-align: center;
        }
        
        .step-number {
            margin-right: 0;
            margin-bottom: 0.5rem;
        }
        
        .upload-area {
            padding: 1rem;
        }
        
        .upload-icon {
            font-size: 2rem;
        }
        
        .grade-badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            min-width: 35px;
        }
        
        .table-responsive {
            font-size: 0.7rem;
        }
        
        .table td, .table th {
            padding: 0.4rem 0.3rem;
        }
    }
</style>

<div class="finder-container">
    <div class="container py-4">
        <div class="step-container">
            <!-- Progress Bar -->
            <div class="mb-5" data-aos="fade-down">
                <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                    <span class="step-indicator active" id="step1Text">
                        <i class="fas fa-file-alt me-2"></i>
                        Step 1: Academic Information
                    </span>
                    <span class="step-indicator" id="step2Text">
                        <i class="fas fa-check-circle me-2"></i>
                        Step 2: Results Verification
                    </span>
                    <span class="step-indicator" id="step3Text">
                        <i class="fas fa-user-graduate me-2"></i>
                        Step 3: Eligibility Profile
                    </span>
                </div>
                <div class="progress-custom">
                    <div class="progress-bar-custom" id="progressBar" style="width: 33%;"></div>
                </div>
            </div>

            <!-- Step 1: Upload SPM -->
            <div class="step-card" id="step1" data-aos="fade-up">
                <div class="step-header">
                    <div class="step-number">1</div>
                    <div>
                        <h4 class="mb-1">Academic Results Submission</h4>
                        <p class="text-muted mb-0">Upload your SPM result slip or enter your results manually.</p>
                    </div>
                </div>
                
                <form id="uploadForm">
                    @csrf
                    <div class="upload-area" id="dropArea">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <h5 class="text-maroon">Drag & Drop or Click to Upload</h5>
                        <p class="text-muted">Supported formats: JPG, PNG, JPEG (Max: 5MB)</p>
                        <input type="file" class="form-control d-none" id="spmFile" name="spm_file" accept="image/*,.pdf" required>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('spmFile').click()">
                            <i class="fas fa-folder-open"></i> Browse Files
                        </button>
                    </div>
                    
                    <div id="fileInfo" class="mt-3 d-none">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-file-alt me-3 fa-2x"></i>
                            <div class="flex-1">
                                <strong id="fileName"></strong>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" id="fileProgress" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="button-group button-group-center mt-4">
                        <button type="button" class="btn btn-primary btn-lg" onclick="processUpload()" id="processBtn">
                            <i class="fas fa-cogs"></i> Process SPM Results
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="skipOCR()">
                            <i class="fas fa-pen"></i> Enter Manually
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Review & Edit OCR Results -->
            <div class="step-card d-none" id="step2" data-aos="fade-up">
                <div class="step-header">
                    <div class="step-number">2</div>
                    <div>
                        <h4 class="mb-1" id="step2Title">Review & Edit Extracted Results</h4>
                        <p class="text-muted mb-0" id="step2Desc">Please edit the extracted grades if needed</p>
                    </div>
                </div>
                
                <div id="ocrResultsContainer">
                    <!-- Results will be loaded here -->
                </div>
            </div>

            <!-- Step 3: Additional Information -->
            <div class="step-card d-none" id="step3" data-aos="fade-up">
                <div class="step-header">
                    <div class="step-number">3</div>
                    <div>
                        <h4 class="mb-1">Eligibility Profile</h4>
                        <p class="text-muted mb-0">Complete your profile for personalized scholarship recommendations.</p>
                    </div>
                </div>
                
                <form id="profileForm" action="{{ route('save.profile') }}" method="POST">
                    @csrf
                    
                    <!-- Extracted Grades Display -->
                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold text-maroon">Verified SPM Results</h6>
                        <div id="extractedGrades">
                            <div class="text-center py-4">
                                <div class="spinner-border text-maroon" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading verified grades...</p>
                            </div>
                        </div>
                        <input type="hidden" name="total_as" id="totalAsInput">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill me-2 text-maroon"></i>
                                Monthly Household Income (RM)
                            </label>
                            <input type="number" name="monthly_income"
                                class="form-control" min="0" placeholder="e.g. 4500" required>
                            <small class="text-muted">
                                B40 ≤ RM4,850 &nbsp;|&nbsp; M40 RM4,851–RM10,960 &nbsp;|&nbsp; T20 > RM10,960
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-road me-2 text-maroon"></i>Intended Study Path
                            </label>
                            <select name="study_path" class="form-select" required>
                                <option value="">-- Select Study Path --</option>
                                <option value="Foundation">Foundation</option>
                                <option value="Matriculation">Matriculation</option>
                                <option value="Diploma">Diploma</option>
                                <option value="Degree">Degree</option>
                                <option value="TVET">TVET</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-book me-2 text-maroon"></i>Field of Study
                            </label>
                            <select name="field_of_study" class="form-select" required>
                                <option value="">-- Select Field --</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Business">Business</option>
                                <option value="Medicine">Medicine</option>
                                <option value="Education">Education</option>
                                <option value="Data Science">Data Science</option>
                                <option value="Finance">Finance</option>
                                <option value="Accounting">Accounting</option>
                                <option value="Economics">Economics</option>
                                <option value="Law">Law</option>
                                <option value="Science">Science</option>
                                <option value="Architecture">Architecture</option>
                                <option value="Social Science">Social Science</option>
                                <option value="Communication">Communication</option>
                                <option value="Hospitality">Hospitality</option>
                                <option value="Art & Design">Art & Design</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-star-of-life me-2 text-maroon"></i>Bumiputera Status
                            </label>
                            <select name="bumiputera" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-birthday-cake me-2 text-maroon"></i>Age
                            </label>
                            <select name="age" class="form-select" required>
                                <option value="">-- Select Age --</option>
                                @for ($i = 15; $i <= 30; $i++)
                                    <option value="{{ $i }}">{{ $i }} Years Old</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt me-2 text-maroon"></i>State of Origin
                            </label>
                            <select name="state" class="form-select" required>
                                <option value="">-- Select State --</option>
                                <option value="Johor">Johor</option>
                                <option value="Kedah">Kedah</option>
                                <option value="Kelantan">Kelantan</option>
                                <option value="Melaka">Melaka</option>
                                <option value="Negeri Sembilan">Negeri Sembilan</option>
                                <option value="Pahang">Pahang</option>
                                <option value="Perak">Perak</option>
                                <option value="Perlis">Perlis</option>
                                <option value="Pulau Pinang">Pulau Pinang</option>
                                <option value="Sabah">Sabah</option>
                                <option value="Sarawak">Sarawak</option>
                                <option value="Selangor">Selangor</option>
                                <option value="Terengganu">Terengganu</option>
                                <option value="Kuala Lumpur">Kuala Lumpur</option>
                                <option value="Putrajaya">Putrajaya</option>
                                <option value="Labuan">Labuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-passport me-2 text-maroon"></i>Citizenship
                            </label>
                            <select name="citizenship" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="International">International</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-trophy me-2 text-maroon"></i>Leadership Experience
                            </label>
                            <select name="has_leadership" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="button-group button-group-between mt-4 pt-3">
                        <button type="button" class="btn btn-outline-primary" onclick="goBackToStep2()">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            Get Recommendations <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // =============================================
    // SWEETALERT2 - FORCE LOAD WITH MULTIPLE CDN
    // =============================================
    (function ensureSwal() {
        if (typeof Swal !== 'undefined' && Swal) {
            console.log('✅ SweetAlert2 already loaded');
            return;
        }
        
        console.log('⏳ Loading SweetAlert2...');
        
        var cdnUrls = [
            'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.all.min.js',
            'https://unpkg.com/sweetalert2@11/dist/sweetalert2.all.min.js'
        ];
        
        var loaded = false;
        
        function tryLoad(index) {
            if (loaded || index >= cdnUrls.length) {
                if (!loaded) {
                    console.warn('⚠️ All SweetAlert2 CDN failed, using native fallback');
                    window.Swal = null;
                }
                return;
            }
            
            var script = document.createElement('script');
            script.src = cdnUrls[index];
            script.async = false;
            
            script.onload = function() {
                if (typeof Swal !== 'undefined' && Swal) {
                    loaded = true;
                    console.log('✅ SweetAlert2 loaded from:', cdnUrls[index]);
                } else {
                    tryLoad(index + 1);
                }
            };
            
            script.onerror = function() {
                console.warn('❌ Failed to load from:', cdnUrls[index]);
                tryLoad(index + 1);
            };
            
            document.head.appendChild(script);
        }
        
        tryLoad(0);
        
        setTimeout(function() {
            if (typeof Swal === 'undefined' || !Swal) {
                console.warn('⚠️ SweetAlert2 timeout, using native fallback');
                window.Swal = null;
            }
        }, 5000);
    })();
    
    // =============================================
    // GET SWAL INSTANCE
    // =============================================
    function getSwal() {
        if (typeof Swal !== 'undefined' && Swal) {
            return Swal;
        }
        return null;
    }
    
    // =============================================
    // SHOW TOAST FALLBACK
    // =============================================
    function showToast(message, type) {
        type = type || 'info';
        var colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        
        var toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed; top: 20px; right: 20px; 
            padding: 16px 24px; border-radius: 12px;
            background: ${colors[type] || '#6b7280'};
            color: white; font-weight: 600;
            z-index: 99999; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 400px; transform: translateX(120%);
            transition: transform 0.4s ease;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(function() {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(function() {
            toast.style.transform = 'translateX(120%)';
            setTimeout(function() {
                if (toast.parentNode) toast.remove();
            }, 400);
        }, 3000);
    }
    
    // =============================================
    // AOS INIT
    // =============================================
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, once: true });
    }
    
    // =============================================
    // VARIABLES
    // =============================================
    let currentStep = 1;
    let ocrData = null;
    
    // =============================================
    // DRAG & DROP
    // =============================================
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('spmFile');
    
    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    if (dropArea) {
        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(function(eventName) {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
    }
    
    function highlight() {
        if (dropArea) dropArea.classList.add('dragover');
    }
    
    function unhighlight() {
        if (dropArea) dropArea.classList.remove('dragover');
    }
    
    if (dropArea) {
        dropArea.addEventListener('drop', handleDrop, false);
    }
    
    function handleDrop(e) {
        var dt = e.dataTransfer;
        var files = dt.files;
        if (fileInput) {
            fileInput.files = files;
            handleFiles(files);
        }
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }
    
    function handleFiles(files) {
        if (files.length > 0) {
            var file = files[0];
            var fileNameEl = document.getElementById('fileName');
            var fileInfoEl = document.getElementById('fileInfo');
            var fileProgressEl = document.getElementById('fileProgress');
            
            if (fileNameEl) fileNameEl.textContent = file.name;
            if (fileInfoEl) fileInfoEl.classList.remove('d-none');
            
            var progress = 0;
            var interval = setInterval(function() {
                progress += 10;
                if (fileProgressEl) fileProgressEl.style.width = progress + '%';
                if (progress >= 100) {
                    clearInterval(interval);
                }
            }, 100);
        }
    }
    
    // =============================================
    // PROCESS UPLOAD
    // =============================================
    function processUpload() {
        var Swal = getSwal();
        var fileInput = document.getElementById('spmFile');
        
        if (!fileInput || !fileInput.files.length) {
            if (Swal) {
                Swal.fire('Error', 'Please select a file to upload', 'error');
            } else {
                showToast('Please select a file to upload', 'error');
            }
            return;
        }
        
        var formData = new FormData(document.getElementById('uploadForm'));
        
        if (Swal) {
            Swal.fire({
                title: 'Processing SPM Certificate',
                html: '<div class="text-center"><div class="spinner-border text-maroon mb-3" role="status"></div><p>Extracting grades using OCR...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });
        } else {
            showToast('Processing SPM Certificate...', 'info');
        }
        
        fetch("{{ route('upload.spm') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (Swal) Swal.close();
            
            if (data.success) {
                ocrData = data;
                displayOCRResults(data);
            } else {
                if (Swal) {
                    Swal.fire('Error', data.message || 'Failed to process SPM certificate', 'error');
                } else {
                    showToast(data.message || 'Failed to process SPM certificate', 'error');
                }
            }
        })
        .catch(function(error) {
            if (Swal) Swal.close();
            if (Swal) {
                Swal.fire('Error', 'Failed to process SPM certificate. Please try again.', 'error');
            } else {
                showToast('Failed to process SPM certificate', 'error');
            }
            console.error('Error:', error);
        });
    }
    
    // =============================================
    // DISPLAY OCR RESULTS
    // =============================================
    function displayOCRResults(data) {
        var isManual = data.manualEntry === true;
        var subjectCount = Object.keys(data.grades).length;
        
        var html = `
            <div class="alert alert-info mb-4 d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-2x"></i>
                <div>
                    <h6 class="mb-1">${isManual ? 'Academic Results Summary' : 'OCR Results Summary'}</h6>
                    <p class="mb-0">${isManual ? 'Added <strong>' + subjectCount + '</strong> subjects' : 'Detected <strong>' + subjectCount + '</strong> subjects'} | 
                    Total A's: <span class="badge bg-success" id="totalAsBadge">${data.totalAs}</span></p>
                </div>
            </div>

            <div id="ocrConfidenceBar" class="mb-4"></div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold text-maroon">${isManual ? 'Enter Your Subjects & Grades' : 'Edit Detected Grades'}</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showAddSubjectModal()">
                        <i class="fas fa-plus"></i> ${isManual ? 'Add Subject' : 'Add Subject'}
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="gradesTable">
                        <thead>
                            <tr>
                                <th width="40%">${isManual ? 'Subject' : 'Subject'}</th>
                                <th width="20%">${isManual ? 'Grade' : 'Grade'}</th>
                                <th width="30%">Edit Grade</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
        `;
        
        var subjects = Object.keys(data.grades);
        subjects.sort();
        
        if (subjects.length === 0) {
            html += `
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-plus-circle fa-2x d-block mb-2" style="color: #d1d5db;"></i>
                        No subjects added yet. Click "Add Subject" to add your SPM results.
                    </td>
                </tr>
            `;
        } else {
            subjects.forEach(function(subject) {
                var grade = data.grades[subject];
                var gradeClass = getGradeClass(grade);
                var safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
                
                html += `
                    <tr id="subject-row-${safeSubjectId}">
                        <td class="fw-bold">${subject}</td>
                        <td><span class="grade-badge ${gradeClass}" id="grade-${safeSubjectId}">${grade}</span></td>
                        <td>
                            <select class="form-select form-select-sm grade-select" data-subject="${subject}" onchange="updateGrade('${subject}', this.value)" id="select-${safeSubjectId}" style="max-width: 140px;">
                                <option value="A+" ${grade === 'A+' ? 'selected' : ''}>A+</option>
                                <option value="A" ${grade === 'A' ? 'selected' : ''}>A</option>
                                <option value="A-" ${grade === 'A-' ? 'selected' : ''}>A-</option>
                                <option value="B+" ${grade === 'B+' ? 'selected' : ''}>B+</option>
                                <option value="B" ${grade === 'B' ? 'selected' : ''}>B</option>
                                <option value="B-" ${grade === 'B-' ? 'selected' : ''}>B-</option>
                                <option value="C+" ${grade === 'C+' ? 'selected' : ''}>C+</option>
                                <option value="C" ${grade === 'C' ? 'selected' : ''}>C</option>
                                <option value="C-" ${grade === 'C-' ? 'selected' : ''}>C-</option>
                                <option value="D" ${grade === 'D' ? 'selected' : ''}>D</option>
                                <option value="E" ${grade === 'E' ? 'selected' : ''}>E</option>
                                <option value="G" ${grade === 'G' ? 'selected' : ''}>G</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSubject('${subject}')" title="Remove this subject">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        
        html += `
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-4 align-items-center">
                    <div class="col-md-7">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> ${isManual ? 'Please enter all your SPM subjects and grades manually before continuing.' : 'Please verify each subject and grade before continuing.'}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="button-group button-group-end mt-3 mt-md-0">
                            <button type="button" class="btn btn-primary" onclick="verifyAndContinue()">
                                <i class="fas fa-check-circle"></i> Continue
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="goBackToUpload()">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        var container = document.getElementById('ocrResultsContainer');
        if (container) container.innerHTML = html;

        if (!isManual && data.confidence !== undefined) {
            var confidence = data.confidence;
            var confidenceColor = 'bg-danger';
            var confidenceText = 'Low confidence – manual verification recommended';

            if (confidence >= 80) {
                confidenceColor = 'bg-success';
                confidenceText = 'High confidence – OCR result is reliable';
            } else if (confidence >= 60) {
                confidenceColor = 'bg-warning';
                confidenceText = 'Medium confidence – please double-check grades';
            }

            var confidenceBar = document.getElementById('ocrConfidenceBar');
            if (confidenceBar) {
                confidenceBar.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label fw-bold">OCR Confidence Level</label>
                        <div class="progress" style="height: 24px;">
                            <div class="progress-bar ${confidenceColor}" role="progressbar" style="width: ${confidence}%; font-weight: 600; font-size: 0.8rem;">
                                ${confidence}%
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">${confidenceText}</small>
                    </div>
                `;
            }
        }

        var step2Title = document.getElementById('step2Title');
        var step2Desc = document.getElementById('step2Desc');
        
        if (isManual) {
            if (step2Title) step2Title.innerText = 'Enter Your SPM Results';
            if (step2Desc) step2Desc.innerText = 'Please add your subjects and grades manually';
        } else {
            if (step2Title) step2Title.innerText = 'Review & Edit Extracted Results';
            if (step2Desc) step2Desc.innerText = 'Please edit the extracted grades if needed';
        }
        
        goToStep(2);
    }
    
    // =============================================
    // GET GRADE CLASS
    // =============================================
    function getGradeClass(grade) {
        if (grade === 'A+') return 'grade-a-plus';
        if (grade === 'A') return 'grade-a';
        if (grade === 'A-') return 'grade-a-minus';
        if (grade === 'B+') return 'grade-b-plus';
        if (grade === 'B') return 'grade-b';
        if (grade === 'B-') return 'grade-b-minus';
        if (grade === 'C+') return 'grade-c-plus';
        if (grade === 'C') return 'grade-c';
        return 'grade-other';
    }
    
    // =============================================
    // UPDATE GRADE
    // =============================================
    function updateGrade(subject, newGrade) {
        if (!ocrData) return;
        ocrData.grades[subject] = newGrade;
        
        var safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
        var badge = document.getElementById('grade-' + safeSubjectId);
        if (badge) {
            badge.textContent = newGrade;
            badge.className = 'grade-badge ' + getGradeClass(newGrade);
        }
        
        var totalAs = 0;
        Object.values(ocrData.grades).forEach(function(grade) {
            if (grade.startsWith('A')) totalAs++;
        });
        
        var badgeEl = document.getElementById('totalAsBadge');
        if (badgeEl) badgeEl.textContent = totalAs;
        ocrData.totalAs = totalAs;
    }
    
    // =============================================
    // SHOW ADD SUBJECT MODAL
    // =============================================
    function showAddSubjectModal() {
        var Swal = getSwal();
        
        if (!Swal) {
            var subject = prompt('Enter subject name:');
            if (subject && subject.trim()) {
                var grade = prompt('Enter grade (A+, A, A-, B+, B, B-, C+, C, C-, D, E, G):');
                if (grade && grade.trim()) {
                    addSubject(subject.trim().toUpperCase(), grade.trim().toUpperCase());
                }
            }
            return;
        }
        
        Swal.fire({
            title: 'Add Subject',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject Name</label>
                        <select id="newSubjectName" class="form-select" onchange="toggleCustomSubject(this.value)">
                            <option value="">-- Select Subject --</option>
                            <option value="BAHASA MELAYU">BAHASA MELAYU</option>
                            <option value="BAHASA INGGERIS">BAHASA INGGERIS</option>
                            <option value="MATHEMATICS">MATHEMATICS</option>
                            <option value="ADDITIONAL MATHEMATICS">ADDITIONAL MATHEMATICS</option>
                            <option value="SCIENCE">SCIENCE</option>
                            <option value="PHYSICS">PHYSICS</option>
                            <option value="CHEMISTRY">CHEMISTRY</option>
                            <option value="BIOLOGY">BIOLOGY</option>
                            <option value="SEJARAH">SEJARAH</option>
                            <option value="PENDIDIKAN ISLAM">PENDIDIKAN ISLAM</option>
                            <option value="PENDIDIKAN MORAL">PENDIDIKAN MORAL</option>
                            <option value="PRINSIP PERAKAUNAN">PRINSIP PERAKAUNAN</option>
                            <option value="EKONOMI">EKONOMI</option>
                            <option value="PERNIAGAAN">PERNIAGAAN</option>
                            <option value="SAINS KOMPUTER">SAINS KOMPUTER</option>
                            <option value="BAHASA ARAB">BAHASA ARAB</option>
                            <option value="KESUSASTERAAN MELAYU">KESUSASTERAAN MELAYU</option>
                            <option value="GEOGRAFI">GEOGRAFI</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                        <input type="text" id="customSubjectName" class="form-control mt-2 d-none" placeholder="Enter custom subject name">
                        <small class="text-muted">Select a subject or choose OTHER to enter custom</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grade</label>
                        <select id="newSubjectGrade" class="form-select">
                            <option value="A+">A+</option>
                            <option value="A">A</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B">B</option>
                            <option value="B-">B-</option>
                            <option value="C+">C+</option>
                            <option value="C">C</option>
                            <option value="C-">C-</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="G">G</option>
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-plus me-2"></i> Add Subject',
            cancelButtonText: '<i class="fas fa-times me-2"></i> Cancel',
            confirmButtonColor: '#7A0019',
            cancelButtonColor: '#6b7280',
            preConfirm: function() {
                var subject = document.getElementById('newSubjectName').value;
                var grade = document.getElementById('newSubjectGrade').value;
                
                if (subject === 'OTHER') {
                    subject = document.getElementById('customSubjectName').value.trim().toUpperCase();
                }
                
                if (!subject) {
                    Swal.showValidationMessage('Please select or enter a subject name');
                    return false;
                }
                
                if (!grade) {
                    Swal.showValidationMessage('Please select a grade');
                    return false;
                }
                
                return { subject: subject, grade: grade };
            }
        }).then(function(result) {
            if (result.isConfirmed && result.value) {
                addSubject(result.value.subject, result.value.grade);
            }
        });
    }

    // =============================================
    // TOGGLE CUSTOM SUBJECT
    // =============================================
    function toggleCustomSubject(value) {
        var customInput = document.getElementById('customSubjectName');
        if (!customInput) return;
        if (value === 'OTHER') {
            customInput.classList.remove('d-none');
        } else {
            customInput.classList.add('d-none');
            customInput.value = '';
        }
    }

    // =============================================
    // ADD SUBJECT
    // =============================================
    function addSubject(subject, grade) {
        var Swal = getSwal();
        
        if (!ocrData) return;
        
        if (ocrData.grades[subject]) {
            if (Swal) {
                Swal.fire('Warning', 'Subject "' + subject + '" already exists!', 'warning');
            } else {
                showToast('Subject "' + subject + '" already exists!', 'warning');
            }
            return;
        }
        
        if (Swal) {
            Swal.fire({
                title: 'Adding Subject',
                html: '<div class="text-center"><div class="spinner-border text-maroon mb-3" role="status"></div><p>Adding subject...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });
        } else {
            showToast('Adding subject...', 'info');
        }
        
        fetch("{{ route('add.ocr.subject') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ subject: subject, grade: grade })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (Swal) Swal.close();
            
            if (data.success) {
                ocrData.grades[subject] = grade;
                ocrData.totalAs = data.totalAs;
                addSubjectToTable(subject, grade, data.totalAs);
                if (Swal) {
                    Swal.fire({ icon: 'success', title: 'Subject Added!', timer: 1500, showConfirmButton: false });
                } else {
                    showToast('Subject added successfully!', 'success');
                }
            } else {
                if (Swal) {
                    Swal.fire('Error', data.message, 'error');
                } else {
                    showToast(data.message || 'Failed to add subject', 'error');
                }
            }
        })
        .catch(function(error) {
            if (Swal) Swal.close();
            console.error(error);
            if (Swal) {
                Swal.fire('Error', 'Failed to add subject', 'error');
            } else {
                showToast('Failed to add subject', 'error');
            }
        });
    }
    
    // =============================================
    // ADD SUBJECT TO TABLE
    // =============================================
    function addSubjectToTable(subject, grade, totalAs) {
        var safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
        var gradeClass = getGradeClass(grade);
        
        // Check if empty state exists and remove it
        var tbody = document.getElementById('gradesTableBody');
        if (tbody) {
            var emptyRow = tbody.querySelector('tr td[colspan="4"]');
            if (emptyRow) {
                tbody.innerHTML = '';
            }
        }
        
        var newRow = document.createElement('tr');
        newRow.id = 'subject-row-' + safeSubjectId;
        newRow.innerHTML = `
            <td class="fw-bold">${subject} <span class="badge bg-success ms-2">Added</span></td>
            <td><span class="grade-badge ${gradeClass}" id="grade-${safeSubjectId}">${grade}</span></td>
            <td>
                <select class="form-select form-select-sm" onchange="updateGrade('${subject}', this.value)" style="max-width: 140px;">
                    <option value="A+" ${grade === 'A+' ? 'selected' : ''}>A+</option>
                    <option value="A" ${grade === 'A' ? 'selected' : ''}>A</option>
                    <option value="A-" ${grade === 'A-' ? 'selected' : ''}>A-</option>
                    <option value="B+" ${grade === 'B+' ? 'selected' : ''}>B+</option>
                    <option value="B" ${grade === 'B' ? 'selected' : ''}>B</option>
                    <option value="B-" ${grade === 'B-' ? 'selected' : ''}>B-</option>
                    <option value="C+" ${grade === 'C+' ? 'selected' : ''}>C+</option>
                    <option value="C" ${grade === 'C' ? 'selected' : ''}>C</option>
                    <option value="C-" ${grade === 'C-' ? 'selected' : ''}>C-</option>
                    <option value="D" ${grade === 'D' ? 'selected' : ''}>D</option>
                    <option value="E" ${grade === 'E' ? 'selected' : ''}>E</option>
                    <option value="G" ${grade === 'G' ? 'selected' : ''}>G</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSubject('${subject}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        if (tbody) tbody.appendChild(newRow);
        
        var badgeEl = document.getElementById('totalAsBadge');
        if (badgeEl) badgeEl.textContent = totalAs;
    }
    
    // =============================================
    // REMOVE SUBJECT
    // =============================================
    function removeSubject(subject) {
        var Swal = getSwal();
        
        function doRemove() {
            fetch("{{ route('remove.ocr.subject') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ subject: subject })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    if (ocrData && ocrData.grades[subject]) delete ocrData.grades[subject];
                    ocrData.totalAs = data.totalAs;
                    var safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
                    var row = document.getElementById('subject-row-' + safeSubjectId);
                    if (row) row.remove();
                    
                    var badgeEl = document.getElementById('totalAsBadge');
                    if (badgeEl) badgeEl.textContent = data.totalAs;
                    
                    // If no subjects left, show empty state
                    var tbody = document.getElementById('gradesTableBody');
                    if (tbody && tbody.children.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-plus-circle fa-2x d-block mb-2" style="color: #d1d5db;"></i>
                                    No subjects added yet. Click "Add Subject" to add your SPM results.
                                </td>
                            </tr>
                        `;
                    }
                    
                    if (Swal) {
                        Swal.fire({ icon: 'success', title: 'Removed!', timer: 2000, showConfirmButton: false });
                    } else {
                        showToast('Subject removed!', 'success');
                    }
                } else { 
                    if (Swal) {
                        Swal.fire('Error', data.message, 'error');
                    } else {
                        showToast(data.message || 'Failed to remove subject', 'error');
                    }
                }
            })
            .catch(function(error) { 
                if (Swal) {
                    Swal.fire('Error', 'Failed to remove subject', 'error');
                } else {
                    showToast('Failed to remove subject', 'error');
                }
            });
        }
        
        if (Swal) {
            Swal.fire({
                title: 'Remove Subject?', 
                text: 'Remove "' + subject + '"?', 
                icon: 'warning',
                showCancelButton: true, 
                confirmButtonText: 'Yes, Remove', 
                confirmButtonColor: '#d33'
            }).then(function(result) {
                if (result.isConfirmed) {
                    doRemove();
                }
            });
        } else {
            if (confirm('Remove "' + subject + '"?')) {
                doRemove();
            }
        }
    }
    
    // =============================================
    // VERIFY AND CONTINUE
    // =============================================
    function verifyAndContinue() {
        var Swal = getSwal();
        
        // Check if there are any subjects
        if (!ocrData || Object.keys(ocrData.grades).length === 0) {
            if (Swal) {
                Swal.fire('Error', 'Please add at least one subject before continuing.', 'error');
            } else {
                showToast('Please add at least one subject before continuing.', 'error');
            }
            return;
        }
        
        function doVerify() {
            fetch("{{ route('verify.ocr.results') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ confirm: true })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    displayVerifiedGrades(data.totalAs);
                    goToStep(3);
                    if (Swal) {
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Verified!', 
                            text: data.message, 
                            timer: 1500, 
                            showConfirmButton: false 
                        });
                    } else {
                        showToast('Verified successfully!', 'success');
                    }
                } else { 
                    if (Swal) {
                        Swal.fire('Error', data.message, 'error');
                    } else {
                        showToast(data.message || 'Verification failed', 'error');
                    }
                }
            })
            .catch(function(error) { 
                if (Swal) {
                    Swal.fire('Error', 'Verification failed', 'error');
                } else {
                    showToast('Verification failed', 'error');
                }
            });
        }
        
        if (Swal) {
            Swal.fire({
                title: 'Verify Results', 
                text: 'Are you sure all grades are correct?', 
                icon: 'question',
                showCancelButton: true, 
                confirmButtonText: 'Yes, Continue', 
                confirmButtonColor: '#3085d6'
            }).then(function(result) {
                if (result.isConfirmed) {
                    doVerify();
                }
            });
        } else {
            if (confirm('Are you sure all grades are correct?')) {
                doVerify();
            }
        }

        // Update grades
        fetch("{{ route('update.ocr.results') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ grades: ocrData.grades })
        });
    }
    
    // =============================================
    // DISPLAY VERIFIED GRADES
    // =============================================
    function displayVerifiedGrades(totalAs) {
        var inputEl = document.getElementById('totalAsInput');
        if (inputEl) inputEl.value = totalAs;
        
        var gradesEl = document.getElementById('extractedGrades');
        if (gradesEl) {
            gradesEl.innerHTML = `
                <div class="verified-grades">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-2x" style="color: #10b981;"></i>
                        <div>
                            <h6 class="mb-1">✓ Verified SPM Results</h6>
                            <p class="mb-0">Total A's: <span class="badge bg-success">${totalAs}</span></p>
                        </div>
                    </div>
                    <p class="text-muted mt-2 mb-0"><small>Your grades have been verified and will be used for scholarship matching.</small></p>
                </div>
            `;
        }
    }
    
    // =============================================
    // GO TO STEP
    // =============================================
    function goToStep(step) {
        var step1 = document.getElementById('step1');
        var step2 = document.getElementById('step2');
        var step3 = document.getElementById('step3');
        var progressBar = document.getElementById('progressBar');
        
        if (step1) step1.classList.add('d-none');
        if (step2) step2.classList.add('d-none');
        if (step3) step3.classList.add('d-none');
        
        var targetStep = document.getElementById('step' + step);
        if (targetStep) targetStep.classList.remove('d-none');
        
        var progress = step === 1 ? 33 : step === 2 ? 66 : 100;
        if (progressBar) progressBar.style.width = progress + '%';
        
        var stepTexts = ['step1Text', 'step2Text', 'step3Text'];
        stepTexts.forEach(function(id, idx) {
            var el = document.getElementById(id);
            if (el) {
                if (idx + 1 === step) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            }
        });
        currentStep = step;
    }
    
    // =============================================
    // GO BACK TO UPLOAD
    // =============================================
    function goBackToUpload() {
        var Swal = getSwal();
        
        function doReset() {
            fetch("{{ route('verify.ocr.results') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ confirm: false })
            }).then(function() {
                var fileInput = document.getElementById('spmFile');
                var fileInfo = document.getElementById('fileInfo');
                
                if (fileInput) fileInput.value = '';
                if (fileInfo) fileInfo.classList.add('d-none');
                goToStep(1);
            });
        }
        
        if (Swal) {
            Swal.fire({
                title: 'Go Back?', 
                text: 'This will clear all extracted data.', 
                icon: 'warning',
                showCancelButton: true, 
                confirmButtonText: 'Yes, Go Back'
            }).then(function(result) {
                if (result.isConfirmed) {
                    doReset();
                }
            });
        } else {
            if (confirm('Go back? This will clear all extracted data.')) {
                doReset();
            }
        }
    }
    
    // =============================================
    // GO BACK TO STEP 2
    // =============================================
    function goBackToStep2() { 
        goToStep(2); 
    }

    // =============================================
    // SKIP OCR
    // =============================================
    function skipOCR() {
        var Swal = getSwal();
        
        function doSkip() {
            fetch("{{ route('ocr.skip') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    ocrData = {
                        grades: {},
                        totalAs: 0,
                        manualEntry: true
                    };
                    displayOCRResults(ocrData);
                }
            });
        }
        
        if (Swal) {
            Swal.fire({
                title: 'Manual Entry',
                text: 'Proceed without OCR and enter SPM results manually?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed'
            }).then(function(result) {
                if (result.isConfirmed) {
                    doSkip();
                }
            });
        } else {
            if (confirm('Proceed without OCR and enter SPM results manually?')) {
                doSkip();
            }
        }
    }
    
    // =============================================
    // PROFILE FORM SUBMIT
    // =============================================
    var profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            var income = document.querySelector('input[name="monthly_income"]');
            var study = document.querySelector('select[name="study_path"]');
            var Swal = getSwal();
            
            if (!income || !income.value || !study || !study.value) {
                e.preventDefault();
                if (Swal) {
                    Swal.fire('Error', 'Please complete all required fields', 'error');
                } else {
                    showToast('Please complete all required fields', 'error');
                }
            }
        });
    }
</script>
@endpush
@endsection