@extends('layouts.admin')

@section('title', 'Manual Scraper Control')

@section('content')

<div class="container-fluid px-0">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>
                        Manual Scraper Control
                    </h5>
                    <p class="text-muted mt-2 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Run web scrapers to collect scholarship data from external sources
                    </p>
                </div>

                <div class="card-body">
                    {{-- SUCCESS OUTPUT --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle me-3 mt-1" style="font-size: 1.5rem;"></i>
                                <div class="flex-grow-1">
                                    <strong class="d-block mb-2">Scraper Executed Successfully!</strong>
                                    <div class="output-container">
                                        <pre class="output-pre">{{ session('success') }}</pre>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ERROR OUTPUT --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle me-3 mt-1" style="font-size: 1.5rem;"></i>
                                <div class="flex-grow-1">
                                    <strong class="d-block mb-2">Error Running Scraper</strong>
                                    <div class="output-container">
                                        <pre class="output-pre">{{ session('error') }}</pre>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- SCRAPER FORM --}}
                    <div class="scraper-form-container">
                        <form method="POST" action="{{ route('admin.scraper.run') }}" id="scraperForm">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-code-branch me-2" style="color: #7A0019;"></i>
                                    Select Scraper Command
                                </label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <select name="command" class="form-select scraper-select" required>
                                            <option value="" disabled selected>-- Choose a scraper to run --</option>
                                            @foreach($commands as $cmd)
                                                <option value="{{ $cmd['command'] }}">
                                                    {{ $cmd['display_name'] }}
                                                    <span class="text-muted small">({{ $cmd['command'] }})</span>
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100" id="runScraperBtn">
                                            <i class="fas fa-play me-2"></i>
                                            Run Scraper
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Select a scraper from the dropdown and click "Run Scraper" to start data collection
                                </small>
                            </div>
                        </form>

                        {{-- Loading Indicator --}}
                        <div id="loadingIndicator" class="loading-indicator" style="display: none;">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="spinner-border text-maroon me-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Scraper is running...</h6>
                                    <p class="text-muted small mb-0">Please wait while we collect scholarship data. This may take a few moments.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Scraper Info Card --}}
                        <div class="info-card mt-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle me-3" style="font-size: 1.5rem; color: #7A0019;"></i>
                                <div>
                                    <h6 class="mb-2 fw-semibold">About Web Scrapers</h6>
                                    <p class="small text-muted mb-2">
                                        Web scrapers automatically collect scholarship information from external websites and save them to your database.
                                        This process may take several seconds to complete depending on the source website.
                                    </p>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="small">Automatic data collection</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="small">Duplicate detection</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="small">Error handling & logging</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Available Scrapers List --}}
                        <div class="mt-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-list me-2" style="color: #7A0019;"></i>
                                Available Scrapers
                            </h6>
                            <div class="row">
                                @foreach($commands as $cmd)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="scraper-item" onclick="selectScraper('{{ $cmd['command'] }}')" style="cursor: pointer;">
                                            <i class="fas fa-cog me-2" style="color: #F4C542;"></i>
                                            <div>
                                                <div class="scraper-name">{{ $cmd['display_name'] }}</div>
                                                <div class="scraper-command small text-muted">{{ $cmd['command'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-bottom: 2px solid #F4C542;
        padding: 20px 25px;
    }
    
    .card-header h5 {
        color: #7A0019;
        font-weight: 700;
        margin: 0;
    }
    
    .form-label {
        color: #374151;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .form-select {
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        padding: 10px 15px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .form-select:focus {
        border-color: #F4C542;
        box-shadow: 0 0 0 3px rgba(244, 197, 66, 0.2);
        outline: none;
    }
    
    .btn-primary {
        background: linear-gradient(115deg, #7A0019, #4e0010);
        border: none;
        padding: 10px 20px;
        border-radius: 40px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(122, 0, 25, 0.3);
        background: linear-gradient(115deg, #4e0010, #7A0019);
    }
    
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .alert {
        border-radius: 16px;
        border: none;
        position: relative;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }
    
    .output-container {
        background: #1e293b;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 15px;
    }
    
    .output-pre {
        background: #1e293b;
        color: #e2e8f0;
        padding: 20px;
        margin: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
        min-height: 300px;
        max-height: 500px;
        overflow-y: auto;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 12px;
        line-height: 1.5;
    }
    
    .loading-indicator {
        background: linear-gradient(135deg, #FFF8EE, #f5ebe0);
        border-radius: 16px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid rgba(244, 197, 66, 0.3);
    }
    
    .spinner-border.text-maroon {
        color: #7A0019;
    }
    
    .info-card {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border-radius: 16px;
        padding: 20px;
        border-left: 4px solid #7A0019;
    }
    
    .scraper-item {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        background: #f9fafb;
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .scraper-item:hover {
        background: rgba(244, 197, 66, 0.1);
        transform: translateX(5px);
        border-color: #F4C542;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .scraper-item.active {
        background: rgba(244, 197, 66, 0.2);
        border-color: #F4C542;
    }
    
    .scraper-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
    }
    
    .scraper-command {
        font-size: 11px;
        color: #6b7280;
    }
    
    .scraper-select option {
        padding: 10px;
    }
    
    /* Custom Scrollbar for Output */
    .output-pre::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .output-pre::-webkit-scrollbar-track {
        background: #0f172a;
        border-radius: 4px;
    }
    
    .output-pre::-webkit-scrollbar-thumb {
        background: #F4C542;
        border-radius: 4px;
    }
    
    .output-pre::-webkit-scrollbar-thumb:hover {
        background: #e6b13e;
    }
    
    @media (max-width: 768px) {
        .card-header {
            padding: 15px 20px;
        }
        
        .btn-primary {
            margin-top: 10px;
        }
        
        .output-pre {
            font-size: 10px;
            padding: 15px;
        }
        
        .info-card {
            padding: 15px;
        }
        
        .scraper-item {
            padding: 8px 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Define the mapping of commands to display names
    const scraperNames = {
        'scrape:all': 'All Scrapers (Complete Collection)',
        'scrape:bnm': 'Bank Negara Malaysia Scholarship',
        'scrape:k.watan': 'Khazanah Watan Scholarship',
        'scrape:k.equity': 'Khazanah Equity Scholarship',
        'scrape:petronas': 'Petronas Scholarship',
        'scrape:jpa.db40': 'JPA DB40 Scholarship',
        'scrape:jpa.lspm': 'JPA LSPM Scholarship',
        'scrape:axiata': 'Axiata Scholarship',
        'scrape:bpmb': 'BPMB Scholarship',
        'scrape:k.paynet': 'PayNet Scholarship',
        'scrape:mar': 'MAR Scholarship',
        'scrape:shell': 'Shell Scholarship',
        'scrape:jpa.ppn': 'JPA PPN Scholarship'
    };

    // Function to select a scraper when clicking on the list item
    function selectScraper(command) {
        // Select the dropdown option
        const select = document.querySelector('.scraper-select');
        select.value = command;
        
        // Trigger change event to update UI
        const event = new Event('change', { bubbles: true });
        select.dispatchEvent(event);
        
        // Highlight the selected scraper item
        document.querySelectorAll('.scraper-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Find and highlight the clicked item
        const items = document.querySelectorAll('.scraper-item');
        items.forEach(item => {
            if (item.textContent.includes(command)) {
                item.classList.add('active');
            }
        });
    }

    // Auto-submit when Enter key is pressed (optional)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            const select = document.querySelector('.scraper-select');
            if (select.value) {
                document.getElementById('scraperForm').submit();
            }
        }
    });

    // Form submission handler
    document.getElementById('scraperForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('runScraperBtn');
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Running Scraper...';
        loadingIndicator.style.display = 'block';
        
        return true;
    });
    
    // Auto-dismiss success alerts after 10 seconds
    setTimeout(function() {
        const successAlerts = document.querySelectorAll('.alert-success');
        successAlerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(() => bsAlert.close(), 8000);
        });
    }, 6000);
</script>
@endpush

@endsection