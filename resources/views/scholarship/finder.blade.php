@extends('layouts.app')

@section('title', 'Find Scholarship')

@section('content')
<style>
    .step-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .step-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .step-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 1rem;
    }
    
    .upload-area {
        border: 2px dashed #3b82f6;
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        background: #eff6ff;
        border-color: #1e40af;
    }
    
    .upload-area.dragover {
        background: #dbeafe;
        border-color: #1e40af;
        transform: scale(1.02);
    }
    
    .upload-icon {
        font-size: 3rem;
        color: #3b82f6;
        margin-bottom: 1rem;
    }
    
    .grade-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        margin: 0.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        min-width: 40px;
        text-align: center;
    }
    
    .grade-a-plus {
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
    }
    
    .grade-a {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
    }
    
    .grade-a-minus {
        background: linear-gradient(135deg, #34d399, #6ee7b7);
        color: white;
    }
    
    .grade-b-plus {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        color: white;
    }
    
    .grade-b {
        background: linear-gradient(135deg, #60a5fa, #93c5fd);
        color: white;
    }
    
    .grade-b-minus {
        background: linear-gradient(135deg, #93c5fd, #bfdbfe);
        color: white;
    }
    
    .grade-c-plus {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
    }
    
    .grade-c {
        background: linear-gradient(135deg, #fbbf24, #fcd34d);
        color: white;
    }
    
    .grade-other {
        background: linear-gradient(135deg, #6b7280, #9ca3af);
        color: white;
    }
    
    #gradesTable th {
        background-color: #f8fafc;
        font-weight: 600;
        color: #374151;
        padding: 0.75rem;
    }
    
    #gradesTable td {
        padding: 0.75rem;
        vertical-align: middle;
    }
    
    .form-select-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
    
    .badge.bg-info {
        font-size: 0.65rem;
        padding: 0.15rem 0.35rem;
    }
    
    .income-option, .study-option {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .income-option:hover, .study-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .income-option.border-primary, .study-option.border-primary {
        background: #eff6ff;
    }
    
    .verified-grades {
        border: 2px solid #10b981;
        border-radius: 12px;
        padding: 1.5rem;
        background: #f0fdf4;
    }
</style>

<div class="container py-5">
    <div class="step-container">
        <!-- Progress Bar -->
        <div class="mb-5">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-primary fw-bold" id="step1Text">Step 1: Upload SPM</span>
                <span class="text-muted" id="step2Text">Step 2: Review Results</span>
                <span class="text-muted" id="step3Text">Step 3: Profile Info</span>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar" id="progressBar" style="width: 33%; background: linear-gradient(135deg, #3b82f6, #1e40af);"></div>
            </div>
        </div>

        <!-- Step 1: Upload SPM -->
        <div class="step-card" id="step1">
            <div class="step-header">
                <div class="step-number">1</div>
                <div>
                    <h4 class="mb-1">Upload Your SPM Certificate</h4>
                    <p class="text-muted mb-0">Upload a clear photo or scan of your SPM results</p>
                </div>
            </div>
            
            <form id="uploadForm">
                @csrf
                <div class="upload-area" id="dropArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h5>Drag & Drop or Click to Upload</h5>
                    <p class="text-muted">Supported formats: JPG, PNG, PDF (Max: 5MB)</p>
                    <input type="file" class="form-control d-none" id="spmFile" name="spm_file" accept="image/*,.pdf" required>
                    <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('spmFile').click()">
                        <i class="fas fa-upload me-2"></i> Choose File
                    </button>
                </div>
                
                <div id="fileInfo" class="mt-3 d-none">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-file-alt me-3 fa-2x"></i>
                        <div>
                            <strong id="fileName"></strong>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar" id="fileProgress" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary btn-lg px-5" onclick="processUpload()" id="processBtn">
                        <i class="fas fa-cogs me-2"></i> Process SPM Results
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 2: Review & Edit OCR Results -->
        <div class="step-card d-none" id="step2">
            <div class="step-header">
                <div class="step-number">2</div>
                <div>
                    <h4 class="mb-1">Review & Edit Extracted Results</h4>
                    <p class="text-muted mb-0">Please verify and edit the extracted grades if needed</p>
                </div>
            </div>
            
            <div id="ocrResultsContainer">
                <!-- Results will be loaded here -->
            </div>
        </div>

        <!-- Step 3: Additional Information -->
        <div class="step-card d-none" id="step3">
            <div class="step-header">
                <div class="step-number">3</div>
                <div>
                    <h4 class="mb-1">Additional Information</h4>
                    <p class="text-muted mb-0">Complete your profile for personalized recommendations</p>
                </div>
            </div>
            
            <form id="profileForm" action="{{ route('save.profile') }}" method="POST">
                @csrf
                
                <!-- Extracted Grades Display -->
                <div class="mb-4">
                    <h6 class="mb-3">Verified SPM Results</h6>
                    <div class="p-3 bg-light rounded" id="extractedGrades">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading verified grades...</p>
                        </div>
                    </div>
                    <input type="hidden" name="academic_category" id="academicCategory">
                </div>
                
                <!-- Family Income -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Family Income Category</label>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card income-option" data-value="B1" onclick="selectIncome('B1')">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-home fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-1">B40</h6>
                                    <small class="text-muted">Below RM 4,850/month</small>
                                    <input type="radio" name="income_category" value="B1" class="d-none" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card income-option" data-value="B3" onclick="selectIncome('B3')">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-building fa-2x text-warning"></i>
                                    </div>
                                    <h6 class="mb-1">M40</h6>
                                    <small class="text-muted">RM 4,851 - RM 10,959/month</small>
                                    <input type="radio" name="income_category" value="B3" class="d-none">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card income-option" data-value="B4" onclick="selectIncome('B4')">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-landmark fa-2x text-success"></i>
                                    </div>
                                    <h6 class="mb-1">T20</h6>
                                    <small class="text-muted">Above RM 10,960/month</small>
                                    <input type="radio" name="income_category" value="B4" class="d-none">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-danger mt-1" id="incomeError"></div>
                </div>
                
                <!-- Study Path -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Intended Study Path</label>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card study-option" data-value="C1" onclick="selectStudy('C1')">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-university fa-2x text-info"></i>
                                    </div>
                                    <h6 class="mb-1">Pre-University</h6>
                                    <input type="radio" name="study_path" value="C1" class="d-none" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card study-option" data-value="C2" onclick="selectStudy('C2')">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-1">Diploma</h6>
                                    <input type="radio" name="study_path" value="C2" class="d-none">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card study-option" data-value="C3" onclick="selectStudy('C3')">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-book-open fa-2x text-warning"></i>
                                    </div>
                                    <h6 class="mb-1">Matriculation</h6>
                                    <input type="radio" name="study_path" value="C3" class="d-none">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card study-option" data-value="C4" onclick="selectStudy('C4')">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-tools fa-2x text-success"></i>
                                    </div>
                                    <h6 class="mb-1">TVET</h6>
                                    <input type="radio" name="study_path" value="C4" class="d-none">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-danger mt-1" id="studyError"></div>
                </div>

                <!-- Bumiputera Status -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Bumiputera Status</label>
                    <select name="bumiputera" class="form-select" required>
                        <option value="">-- Select --</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <!-- Age -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Age</label>
                    <input type="number"
                        name="age"
                        class="form-control"
                        min="15"
                        max="30"
                        placeholder="Enter your age"
                        required>
                </div>

                <!-- Gender -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="">-- Select --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <!-- State -->
                <div class="mb-4">
                    <label class="form-label fw-bold">State of Origin</label>
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

                <!-- Leadership Experience -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Leadership Experience</label>
                    <select name="has_leadership" class="form-select" required>
                        <option value="">-- Select --</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>



                
                <!-- Navigation -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-primary" onclick="goBackToStep2()">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        Get Recommendations <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
            
            // Simulate upload progress
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
        
        // Show loading
        Swal.fire({
            title: 'Processing SPM Certificate',
            html: '<div class="text-center"><div class="spinner-border text-primary mb-3" role="status"></div><p>Extracting grades using OCR...</p></div>',
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
        let html = `
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h6 class="mb-1">OCR Results Summary</h6>
                        <p class="mb-0">Detected <strong>${Object.keys(data.grades).length}</strong> subjects | 
                        Total A's: <span class="badge bg-success" id="totalAsBadge">${data.totalAs}</span> | 
                        Academic Category: <span class="badge bg-primary" id="academicCategoryBadge">${data.academicCategory}</span></p>
                    </div>
                </div>
            </div>

            <div id="ocrConfidenceBar" class="mb-4"></div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Edit Detected Grades:</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="showAddSubjectModal()">
                            <i class="fas fa-plus me-1"></i> Add Missing Subject
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="saveAllGrades()">
                            <i class="fas fa-save me-1"></i> Save All Changes
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="gradesTable">
                        <thead>
                            <tr>
                                <th width="40%">Subject (Detected by OCR)</th>
                                <th width="20%">Extracted Grade</th>
                                <th width="30%">Edit Grade</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
        `;
        
        // Sort subjects alphabetically
        const subjects = Object.keys(data.grades);
        subjects.sort();
        
        subjects.forEach(subject => {
            const grade = data.grades[subject];
            const gradeClass = getGradeClass(grade);
            const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
            
            html += `
                <tr id="subject-row-${safeSubjectId}">
                    <td class="fw-bold">${subject}</td>
                    <td>
                        <span class="grade-badge ${gradeClass}" id="grade-${safeSubjectId}">${grade}</span>
                    </td>
                    <td>
                        <select class="form-select form-select-sm grade-select" data-subject="${subject}" onchange="updateGrade('${subject}', this.value)" id="select-${safeSubjectId}" style="max-width: 120px;">
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
                
                <div class="row mt-3">
                    <div class="col-md-8">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>OCR Accuracy Note:</h6>
                            <p class="mb-0 small">OCR may not detect all subjects or may misread grades. 
                            Please verify each subject and grade. Add any missing subjects using the button above.</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" onclick="verifyAndContinue()">
                                <i class="fas fa-check-circle me-2"></i> Verify & Continue
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="goBackToUpload()">
                                <i class="fas fa-redo me-2"></i> Upload Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('ocrResultsContainer').innerHTML = html;

        // 🔍 Render OCR Confidence Bar
if (data.confidence !== undefined) {
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
            <div class="progress-bar ${confidenceColor}"
                 role="progressbar"
                 style="width: ${confidence}%"
                 aria-valuenow="${confidence}"
                 aria-valuemin="0"
                 aria-valuemax="100">
                ${confidence}%
            </div>
        </div>
        <small class="text-muted mt-1 d-block">${confidenceText}</small>
    `;
}

        
        // Move to step 2
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
    
    // Update single grade locally
    function updateGrade(subject, newGrade) {
        if (!ocrData) return;
        
        // Update local data
        ocrData.grades[subject] = newGrade;
        
        // Update badge display
        const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
        const badge = document.getElementById(`grade-${safeSubjectId}`);
        if (badge) {
            badge.textContent = newGrade;
            badge.className = `grade-badge ${getGradeClass(newGrade)}`;
        }
        
        // Recalculate total A's
        let totalAs = 0;
        Object.values(ocrData.grades).forEach(grade => {
            if (grade.startsWith('A')) {
                totalAs++;
            }
        });
        
        // Update academic category
        let academicCategory = 'A1';
        if (totalAs >= 10) academicCategory = 'A4';
        else if (totalAs >= 7) academicCategory = 'A3';
        else if (totalAs >= 4) academicCategory = 'A2';
        
        // Update display
        document.getElementById('totalAsBadge').textContent = totalAs;
        document.getElementById('academicCategoryBadge').textContent = academicCategory;
        
        // Update data
        ocrData.totalAs = totalAs;
        ocrData.academicCategory = academicCategory;
    }
    
    // Save all grades to server
    function saveAllGrades() {
        if (!ocrData) return;
        
        Swal.fire({
            title: 'Saving Changes',
            html: '<div class="text-center"><div class="spinner-border text-primary mb-3" role="status"></div><p>Saving grade changes...</p></div>',
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
                // Update local data with server response
                ocrData.totalAs = data.totalAs;
                ocrData.academicCategory = data.academicCategory;
                
                // Update display
                document.getElementById('totalAsBadge').textContent = data.totalAs;
                document.getElementById('academicCategoryBadge').textContent = data.academicCategory;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Grades Saved!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Error', 'Failed to save grades', 'error');
        });
    }
    
    // Show modal to add missing subject
    function showAddSubjectModal() {
        Swal.fire({
            title: 'Add Missing Subject',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" id="newSubjectName" class="form-control" placeholder="e.g., BAHASA ARAB" required>
                        <small class="text-muted">Enter subject name in capital letters (e.g., BAHASA MELAYU)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade</label>
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
            confirmButtonText: 'Add Subject',
            cancelButtonText: 'Cancel',
            focusConfirm: false,
            preConfirm: () => {
                const subject = document.getElementById('newSubjectName').value;
                const grade = document.getElementById('newSubjectGrade').value;
                
                if (!subject.trim()) {
                    Swal.showValidationMessage('Please enter subject name');
                    return false;
                }
                
                if (subject.trim().length < 3) {
                    Swal.showValidationMessage('Subject name must be at least 3 characters');
                    return false;
                }
                
                return { subject: subject.trim().toUpperCase(), grade: grade };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                addSubject(result.value.subject, result.value.grade);
            }
        });
    }
    
    // Add new subject
    function addSubject(subject, grade) {
        if (!ocrData) return;
        
        // Check if subject already exists
        if (ocrData.grades[subject]) {
            Swal.fire('Warning', `Subject "${subject}" already exists!`, 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Adding Subject',
            html: '<div class="text-center"><div class="spinner-border text-primary mb-3" role="status"></div><p>Adding new subject...</p></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });
        
        fetch("{{ route('add.ocr.subject') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ subject: subject, grade: grade })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Update local data
                ocrData.grades[subject] = grade;
                ocrData.totalAs = data.totalAs;
                ocrData.academicCategory = data.academicCategory;
                
                // Add new row to table
                addSubjectToTable(subject, grade, data.totalAs, data.academicCategory);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Subject Added!',
                    text: `${subject} added successfully.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Error', 'Failed to add subject', 'error');
        });
    }
    
    // Add new subject row to table
    function addSubjectToTable(subject, grade, totalAs, academicCategory) {
        const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
        const gradeClass = getGradeClass(grade);
        
        const newRow = `
            <tr id="subject-row-${safeSubjectId}">
                <td class="fw-bold">${subject} <span class="badge bg-info ms-2">Added</span></td>
                <td>
                    <span class="grade-badge ${gradeClass}" id="grade-${safeSubjectId}">${grade}</span>
                </td>
                <td>
                    <select class="form-select form-select-sm grade-select" data-subject="${subject}" onchange="updateGrade('${subject}', this.value)" id="select-${safeSubjectId}" style="max-width: 120px;">
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
        
        // Add to table body
        const tbody = document.getElementById('gradesTableBody');
        if (tbody) {
            tbody.innerHTML += newRow;
        }
        
        // Update summary
        document.getElementById('totalAsBadge').textContent = totalAs;
        document.getElementById('academicCategoryBadge').textContent = academicCategory;
    }
    
    // Remove subject
    function removeSubject(subject) {
        Swal.fire({
            title: 'Remove Subject?',
            text: `Are you sure you want to remove "${subject}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('remove.ocr.subject') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ subject: subject })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove from local data
                        if (ocrData && ocrData.grades[subject]) {
                            delete ocrData.grades[subject];
                            ocrData.totalAs = data.totalAs;
                            ocrData.academicCategory = data.academicCategory;
                        }
                        
                        // Remove row from table
                        const safeSubjectId = subject.replace(/[^a-zA-Z0-9]/g, '-');
                        const row = document.getElementById(`subject-row-${safeSubjectId}`);
                        if (row) {
                            row.remove();
                        }
                        
                        // Update summary
                        document.getElementById('totalAsBadge').textContent = data.totalAs;
                        document.getElementById('academicCategoryBadge').textContent = data.academicCategory;
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed!',
                            text: `Subject "${subject}" removed successfully.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Failed to remove subject', 'error');
                });
            }
        });
    }
    
    function verifyAndContinue() {
        Swal.fire({
            title: 'Verify Results',
            text: 'Are you sure all grades are correct?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Continue',
            cancelButtonText: 'No, Edit Again',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                // Verify with server
                fetch("{{ route('verify.ocr.results') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ confirm: true })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Display verified grades in step 3
                        displayVerifiedGrades(data.totalAs, data.academicCategory);
                        
                        // Move to step 3
                        goToStep(3);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Verification failed', 'error');
                });
            }
        });
    }
    
    function displayVerifiedGrades(totalAs, academicCategory) {
        const gradesContainer = document.getElementById('extractedGrades');
        gradesContainer.innerHTML = `
            <div class="verified-grades">
                <div class="alert alert-success">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-2x"></i>
                        <div>
                            <h6 class="mb-1">✓ Verified SPM Results</h6>
                            <p class="mb-0">Total A's: <span class="badge bg-success">${totalAs}</span> | 
                            Academic Category: <span class="badge bg-primary">${academicCategory}</span></p>
                        </div>
                    </div>
                </div>
                <p class="text-muted mb-0"><small>Your grades have been verified and will be used for scholarship matching.</small></p>
            </div>
        `;
        
        // Set academic category in hidden field
        document.getElementById('academicCategory').value = academicCategory;
    }
    
    // Navigation functions
    function goToStep(step) {
        // Hide all steps
        document.getElementById('step1').classList.add('d-none');
        document.getElementById('step2').classList.add('d-none');
        document.getElementById('step3').classList.add('d-none');
        
        // Show current step
        document.getElementById(`step${step}`).classList.remove('d-none');
        
        // Update progress bar
        const progress = step === 1 ? 33 : step === 2 ? 66 : 100;
        document.getElementById('progressBar').style.width = `${progress}%`;
        
        // Update step text
        document.querySelectorAll('.d-flex.justify-content-between span').forEach(span => {
            span.classList.remove('text-primary', 'fw-bold');
            span.classList.add('text-muted');
        });
        
        document.getElementById(`step${step}Text`).classList.remove('text-muted');
        document.getElementById(`step${step}Text`).classList.add('text-primary', 'fw-bold');
        
        currentStep = step;
    }
    
    function goBackToUpload() {
        Swal.fire({
            title: 'Upload Again?',
            text: 'This will clear all extracted data.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Upload Again',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear OCR data
                fetch("{{ route('verify.ocr.results') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ confirm: false })
                })
                .then(() => {
                    // Reset file input
                    document.getElementById('spmFile').value = '';
                    document.getElementById('fileInfo').classList.add('d-none');
                    
                    // Go back to step 1
                    goToStep(1);
                });
            }
        });
    }
    
    function goBackToStep2() {
        goToStep(2);
    }
    
    // Selection functions for step 3
    function selectIncome(value) {
        document.querySelectorAll('.income-option').forEach(option => {
            option.classList.remove('border-primary', 'border-2');
            option.querySelector('input[type="radio"]').checked = false;
        });
        
        const selected = document.querySelector(`.income-option[data-value="${value}"]`);
        selected.classList.add('border-primary', 'border-2');
        selected.querySelector('input[type="radio"]').checked = true;
        document.getElementById('incomeError').textContent = '';
    }
    
    function selectStudy(value) {
        document.querySelectorAll('.study-option').forEach(option => {
            option.classList.remove('border-primary', 'border-2');
            option.querySelector('input[type="radio"]').checked = false;
        });
        
        const selected = document.querySelector(`.study-option[data-value="${value}"]`);
        selected.classList.add('border-primary', 'border-2');
        selected.querySelector('input[type="radio"]').checked = true;
        document.getElementById('studyError').textContent = '';
    }
    
    // Form validation for step 3
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const incomeSelected = document.querySelector('input[name="income_category"]:checked');
        const studySelected = document.querySelector('input[name="study_path"]:checked');
        let valid = true;
        
        if (!incomeSelected) {
            document.getElementById('incomeError').textContent = 'Please select family income category';
            valid = false;
        }
        
        if (!studySelected) {
            document.getElementById('studyError').textContent = 'Please select intended study path';
            valid = false;
        }
        
        if (!valid) {
            e.preventDefault();
            Swal.fire('Error', 'Please complete all required fields', 'error');
        }
    });
</script>
@endpush
@endsection