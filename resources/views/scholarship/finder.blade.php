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
    }
    
    .step-indicator.active {
        color: var(--maroon);
        font-weight: 700;
    }
    
    /* Upload Area */
    .upload-area {
        border: 2px dashed var(--maroon);
        border-radius: 20px;
        padding: 3rem;
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
        font-size: 3rem;
        color: var(--maroon);
        margin-bottom: 1rem;
    }
    
    /* Grade Badges */
    .grade-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        margin: 0.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        min-width: 50px;
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
        min-width: 120px;
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
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        min-height: 36px;
        min-width: 80px;
    }
    
    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1.05rem;
        min-height: 52px;
        min-width: 160px;
    }
    
    .btn-block {
        width: 100%;
        min-width: unset;
    }
    
    /* Button Container */
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
    .table th {
        background: linear-gradient(135deg, var(--cream), var(--cream-dark));
        color: var(--maroon);
        font-weight: 700;
        border-bottom: 2px solid var(--gold);
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
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
    
    /* Alerts */
    .alert-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border: none;
        border-left: 4px solid #3b82f6;
        border-radius: 12px;
        color: #1e40af;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: none;
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        color: #92400e;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-left: 4px solid #10b981;
        border-radius: 12px;
        color: #065f46;
    }
    
    .alert {
        border-radius: 12px;
    }
    
    /* Utility Classes */
    .text-maroon {
        color: var(--maroon);
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
    .d-grid { display: grid; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .step-card {
            padding: 1.25rem;
        }
        
        .upload-area {
            padding: 1.5rem;
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
            min-width: 100px;
        }
        
        .btn-lg {
            padding: 0.6rem 1.25rem;
            font-size: 0.95rem;
            min-height: 46px;
            min-width: 140px;
        }
        
        .btn-sm {
            padding: 0.3rem 0.75rem;
            font-size: 0.8rem;
            min-height: 32px;
            min-width: 60px;
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
            min-width: unset;
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
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            min-width: 40px;
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
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
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
                            <i class="fas fa-pen"></i> I dont have SPM result
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
                        <!-- Income Category -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-chart-line me-2 text-maroon"></i>Family Income Category
                            </label>
                            <select name="income_category" class="form-select" required>
                                <option value="">-- Select Income Category --</option>
                                <option value="B40">B40 (Below RM 5,351)</option>
                                <option value="M40">M40 (RM 5,351 - RM 11,819)</option>
                                <option value="T20">T20 (Above RM 11,819)</option>
                            </select>
                        </div>

                        <!-- Study Path -->
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
                                <option value="Postgraduate">Postgraduate</option>
                            </select>
                        </div>

                        <!-- Field of Study -->
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

                        <!-- Bumiputera Status -->
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

                        <!-- Age -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-birthday-cake me-2 text-maroon"></i>Age
                            </label>
                            <input type="number" name="age" class="form-control" min="15" max="30" placeholder="Enter your age" required>
                        </div>

                        <!-- State -->
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

                        <!-- Citizenship -->
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

                        <!-- Leadership Experience -->
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
                    
                    <!-- Navigation -->
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    // Current step tracking
    let currentStep = 1;
    let ocrData = null;
    
    // Drag and drop functionality
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('spmFile');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropArea.classList.add('dragover');
    }
    
    function unhighlight() {
        dropArea.classList.remove('dragover');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFiles(files);
    }
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileInfo').classList.remove('d-none');
            
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                document.getElementById('fileProgress').style.width = progress + '%';
                if (progress >= 100) {
                    clearInterval(interval);
                }
            }, 100);
        }
    }
    
    // OCR Processing
    function processUpload() {
        const fileInput = document.getElementById('spmFile');
        if (!fileInput.files.length) {
            Swal.fire('Error', 'Please select a file to upload', 'error');
            return;
        }
        
        const formData = new FormData(document.getElementById('uploadForm'));
        
        Swal.fire({
            title: 'Processing SPM Certificate',
            html: '<div class="text-center"><div class="spinner-border text-maroon mb-3" role="status"></div><p>Extracting grades using OCR...</p></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });
        
        fetch("{{ route('upload.spm') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                ocrData = data;
                displayOCRResults(data);
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Error', 'Failed to process SPM certificate', 'error');
            console.error('Error:', error);
        });
    }
    
    // Display OCR results in editable table
    function displayOCRResults(data) {
        const isManual = data.manualEntry === true;

        let html = `
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h6 class="mb-1">${isManual ? 'Academic Results Summary' : 'OCR Results Summary'}</h6>
                        <p class="mb-0">${isManual ? `Added <strong>${Object.keys(data.grades).length}</strong> subjects` : `Detected <strong>${Object.keys(data.grades).length}</strong> subjects`} | 
                        Total A's: <span class="badge bg-success" id="totalAsBadge">${data.totalAs}</span></p>
                    </div>
                </div>
            </div>

            <div id="ocrConfidenceBar" class="mb-4"></div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold text-maroon">${isManual ? 'Enter Your Subjects & Grades' : 'Edit Detected Grades'}</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showAddSubjectModal()">
                        <i class="fas fa-plus"></i> ${isManual ? 'Add Subject' : 'Add Missing Subject'}
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="gradesTable">
                        <thead>
                            <tr>
                                <th width="40%">${isManual ? 'Subject' : 'Subject (Detected by OCR)'}</th>
                                <th width="20%">${isManual ? 'Grade' : 'Extracted Grade'}</th>
                                <th width="30%">Edit Grade</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
        `;
        
        const subjects = Object.keys(data.grades);
        subjects.sort();
        
        subjects.forEach(subject => {
            const grade = data.grades[subject];
            const gradeClass = getGradeClass(grade);
            const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
            
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
        
        html += `
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3 align-items-center">
                    <div class="col-md-8">
                        <div class="alert alert-warning mb-0">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>${isManual ? 'Manual Entry Note:' : 'OCR Accuracy Note:'}</h6>
                            <p class="mb-0 small">${isManual ? 'Please enter all your SPM subjects and grades manually before continuing.' : 'OCR may not detect all subjects or may misread grades. Please verify each subject and grade.'}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="button-group button-group-end mt-3 mt-md-0">
                            <button type="button" class="btn btn-primary" onclick="verifyAndContinue()">
                                <i class="fas fa-check-circle"></i> Continue
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="goBackToUpload()">
                                <i class="fas fa-redo"></i> ${isManual ? 'Back' : 'Upload Again'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('ocrResultsContainer').innerHTML = html;

        if (!isManual && data.confidence !== undefined) {
            const confidence = data.confidence;
            let confidenceColor = 'bg-danger';
            let confidenceText = 'Low confidence – manual verification recommended';

            if (confidence >= 80) {
                confidenceColor = 'bg-success';
                confidenceText = 'High confidence – OCR result is reliable';
            } else if (confidence >= 60) {
                confidenceColor = 'bg-warning';
                confidenceText = 'Medium confidence – please double-check grades';
            }

            document.getElementById('ocrConfidenceBar').innerHTML = `
                <label class="form-label fw-bold">OCR Confidence Level</label>
                <div class="progress" style="height: 22px;">
                    <div class="progress-bar ${confidenceColor}" role="progressbar" style="width: ${confidence}%" aria-valuenow="${confidence}" aria-valuemin="0" aria-valuemax="100">
                        ${confidence}%
                    </div>
                </div>
                <small class="text-muted mt-1 d-block">${confidenceText}</small>
            `;
        }

        if (isManual) {
            document.getElementById('step2Title').innerText = 'Enter Your SPM Results';
            document.getElementById('step2Desc').innerText = 'Please add your subjects and grades manually';
        } else {
            document.getElementById('step2Title').innerText = 'Review & Edit Extracted Results';
            document.getElementById('step2Desc').innerText = 'Please edit the extracted grades if needed';
        }
        
        goToStep(2);
    }
    
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
    
    function updateGrade(subject, newGrade) {
        if (!ocrData) return;
        ocrData.grades[subject] = newGrade;
        
        const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
        const badge = document.getElementById(`grade-${safeSubjectId}`);
        if (badge) {
            badge.textContent = newGrade;
            badge.className = `grade-badge ${getGradeClass(newGrade)}`;
        }
        
        let totalAs = 0;
        Object.values(ocrData.grades).forEach(grade => {
            if (grade.startsWith('A')) totalAs++;
        });
        
        document.getElementById('totalAsBadge').textContent = totalAs;
        ocrData.totalAs = totalAs;
    }
    
    function saveAllGrades() {
        if (!ocrData) return;
        
        Swal.fire({
            title: 'Saving Changes',
            html: '<div class="text-center"><div class="spinner-border text-maroon mb-3" role="status"></div><p>Saving grade changes...</p></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });
        
        fetch("{{ route('update.ocr.results') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ grades: ocrData.grades })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                ocrData.totalAs = data.totalAs;
                document.getElementById('totalAsBadge').textContent = data.totalAs;
                Swal.fire({ icon: 'success', title: 'Grades Saved!', text: data.message, timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Error', 'Failed to save grades', 'error');
        });
    }
    
    function showAddSubjectModal() {
        Swal.fire({
            title: 'Add Missing Subject',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" id="newSubjectName" class="form-control" placeholder="e.g., BAHASA ARAB" required>
                        <small class="text-muted">Enter subject name in capital letters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <select id="newSubjectGrade" class="form-select">
                            <option value="A+">A+</option><option value="A">A</option><option value="A-">A-</option>
                            <option value="B+">B+</option><option value="B">B</option><option value="B-">B-</option>
                            <option value="C+">C+</option><option value="C">C</option><option value="C-">C-</option>
                            <option value="D">D</option><option value="E">E</option><option value="G">G</option>
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Add Subject',
            preConfirm: () => {
                const subject = document.getElementById('newSubjectName').value;
                const grade = document.getElementById('newSubjectGrade').value;
                if (!subject.trim()) { Swal.showValidationMessage('Please enter subject name'); return false; }
                if (subject.trim().length < 3) { Swal.showValidationMessage('Subject name must be at least 3 characters'); return false; }
                return { subject: subject.trim().toUpperCase(), grade: grade };
            }
        }).then((result) => {
            if (result.isConfirmed) addSubject(result.value.subject, result.value.grade);
        });
    }
    
    function addSubject(subject, grade) {
        if (!ocrData) return;
        if (ocrData.grades[subject]) {
            Swal.fire('Warning', `Subject "${subject}" already exists!`, 'warning');
            return;
        }
        
        Swal.fire({ title: 'Adding Subject', html: '<div class="spinner-border text-maroon"></div>', allowOutsideClick: false, showConfirmButton: false });
        
        fetch("{{ route('add.ocr.subject') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ subject: subject, grade: grade })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                ocrData.grades[subject] = grade;
                ocrData.totalAs = data.totalAs;
                addSubjectToTable(subject, grade, data.totalAs);
                Swal.fire({ icon: 'success', title: 'Subject Added!', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => { Swal.close(); Swal.fire('Error', 'Failed to add subject', 'error'); });
    }
    
    function addSubjectToTable(subject, grade, totalAs) {
        const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
        const gradeClass = getGradeClass(grade);
        const newRow = `
            <tr id="subject-row-${safeSubjectId}">
                <td class="fw-bold">${subject} <span class="badge bg-info ms-2">Added</span></td>
                <td><span class="grade-badge ${gradeClass}" id="grade-${safeSubjectId}">${grade}</span></td>
                <td><select class="form-select form-select-sm" onchange="updateGrade('${subject}', this.value)" style="max-width: 140px;">
                    <option value="A+" ${grade === 'A+' ? 'selected' : ''}>A+</option><option value="A" ${grade === 'A' ? 'selected' : ''}>A</option>
                    <option value="A-" ${grade === 'A-' ? 'selected' : ''}>A-</option><option value="B+" ${grade === 'B+' ? 'selected' : ''}>B+</option>
                    <option value="B" ${grade === 'B' ? 'selected' : ''}>B</option><option value="B-" ${grade === 'B-' ? 'selected' : ''}>B-</option>
                    <option value="C+" ${grade === 'C+' ? 'selected' : ''}>C+</option><option value="C" ${grade === 'C' ? 'selected' : ''}>C</option>
                    <option value="C-" ${grade === 'C-' ? 'selected' : ''}>C-</option><option value="D" ${grade === 'D' ? 'selected' : ''}>D</option>
                    <option value="E" ${grade === 'E' ? 'selected' : ''}>E</option><option value="G" ${grade === 'G' ? 'selected' : ''}>G</option>
                </select></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSubject('${subject}')"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        const tbody = document.getElementById('gradesTableBody');
        if (tbody) tbody.innerHTML += newRow;
        document.getElementById('totalAsBadge').textContent = totalAs;
    }
    
    function removeSubject(subject) {
        Swal.fire({
            title: 'Remove Subject?', text: `Remove "${subject}"?`, icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Yes, Remove', confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('remove.ocr.subject') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ subject: subject })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (ocrData && ocrData.grades[subject]) delete ocrData.grades[subject];
                        ocrData.totalAs = data.totalAs;
                        const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
                        const row = document.getElementById(`subject-row-${safeSubjectId}`);
                        if (row) row.remove();
                        document.getElementById('totalAsBadge').textContent = data.totalAs;
                        Swal.fire({ icon: 'success', title: 'Removed!', timer: 2000, showConfirmButton: false });
                    } else { Swal.fire('Error', data.message, 'error'); }
                })
                .catch(error => { Swal.fire('Error', 'Failed to remove subject', 'error'); });
            }
        });
    }
    
    function verifyAndContinue() {
        Swal.fire({
            title: 'Verify Results', text: 'Are you sure all grades are correct?', icon: 'question',
            showCancelButton: true, confirmButtonText: 'Yes, Continue', confirmButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('verify.ocr.results') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ confirm: true })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayVerifiedGrades(data.totalAs);
                        goToStep(3);
                        Swal.fire({ icon: 'success', title: 'Verified!', text: data.message, timer: 1500, showConfirmButton: false });
                    } else { Swal.fire('Error', data.message, 'error'); }
                })
                .catch(error => { Swal.fire('Error', 'Verification failed', 'error'); });
            }
        });

        fetch("{{ route('update.ocr.results') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                grades: ocrData.grades
            })
        });
    }
    
    function displayVerifiedGrades(totalAs) {
        document.getElementById('totalAsInput').value = totalAs;
        document.getElementById('extractedGrades').innerHTML = `
            <div class="verified-grades">
                <div class="alert alert-success">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-2x"></i>
                        <div>
                            <h6 class="mb-1">✓ Verified SPM Results</h6>
                            <p class="mb-0">Total A's: <span class="badge bg-success">${totalAs}</span></p>
                        </div>
                    </div>
                </div>
                <p class="text-muted mb-0"><small>Your grades have been verified and will be used for scholarship matching.</small></p>
            </div>
        `;
    }
    
    function goToStep(step) {
        document.getElementById('step1').classList.add('d-none');
        document.getElementById('step2').classList.add('d-none');
        document.getElementById('step3').classList.add('d-none');
        document.getElementById(`step${step}`).classList.remove('d-none');
        
        const progress = step === 1 ? 33 : step === 2 ? 66 : 100;
        document.getElementById('progressBar').style.width = `${progress}%`;
        
        const stepTexts = ['step1Text', 'step2Text', 'step3Text'];
        stepTexts.forEach((id, idx) => {
            const el = document.getElementById(id);
            if (idx + 1 === step) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });
        currentStep = step;
    }
    
    function goBackToUpload() {
        Swal.fire({
            title: 'Upload Again?', text: 'This will clear all extracted data.', icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Yes, Upload Again'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('verify.ocr.results') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ confirm: false })
                }).then(() => {
                    document.getElementById('spmFile').value = '';
                    document.getElementById('fileInfo').classList.add('d-none');
                    goToStep(1);
                });
            }
        });
    }
    
    function goBackToStep2() { goToStep(2); }

    function skipOCR() {
        Swal.fire({
            title: 'Manual Entry',
            text: 'Proceed without OCR and enter SPM results manually?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('ocr.skip') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
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
        });
    }
    
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const incomeSelected = document.querySelector('select[name="income_category"]').value;
        const studySelected = document.querySelector('select[name="study_path"]').value;
        if (!incomeSelected || !studySelected) {
            e.preventDefault();
            Swal.fire('Error', 'Please complete all required fields', 'error');
        }
    });
</script>
@endpush
@endsection