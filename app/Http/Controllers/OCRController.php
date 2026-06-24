<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Storage;

class OCRController extends Controller
{
    /**
     * Allowed grade values with their point values for ranking
     */
    const GRADE_POINTS = [
        'A+' => 12,
        'A' => 11,
        'A-' => 10,
        'B+' => 9,
        'B' => 8,
        'B-' => 7,
        'C+' => 6,
        'C' => 5,
        'C-' => 4,
        'D' => 3,
        'E' => 2,
        'G' => 1,
    ];

    /**
     * Common OCR misreadings mapping
     */
    const OCR_CORRECTIONS = [
        'AT' => 'A+',
        'AS' => 'A+',
        'A®' => 'A+',
        'A?' => 'A+',
        'A*' => 'A+',
        'A€' => 'A+',
        'A½' => 'A+',
        'A»' => 'A+',
        'A~' => 'A+',
        'A#' => 'A+',
        'BT' => 'B+',
        'BS' => 'B+',
        'B®' => 'B+',
        'B?' => 'B+',
        'B*' => 'B+',
        'B#' => 'B+',
        'CT' => 'C+',
        'CS' => 'C+',
        'C®' => 'C+',
        'C?' => 'C+',
        'C*' => 'C+',
        'B-' => 'B-',
        'C-' => 'C-',
        'A-' => 'A-',
        'B +' => 'B+',
        'C +' => 'C+',
        'A +' => 'A+',
        'B -' => 'B-',
        'C -' => 'C-',
        'A -' => 'A-',
    ];

    /**
     * Comprehensive subject list with common variations
     * Only include subjects that actually appear in SPM
     */
    const SUBJECTS = [
        'BAHASA MELAYU' => ['BM', 'BAHASA MALAYSIA', 'MELAYU'],
        'BAHASA INGGERIS' => ['ENGLISH', 'BI', 'INGGERIS'],
        'PENDIDIKAN ISLAM' => ['PI', 'ISLAM', 'PAI'],
        'PENDIDIKAN MORAL' => ['PM', 'MORAL'],
        'SEJARAH' => ['SEJ', 'HISTORY'],
        'MATHEMATICS' => ['MATH', 'MATEMATIK', 'MM'],
        'ADDITIONAL MATHEMATICS' => ['ADD MATH', 'MATEMATIK TAMBAHAN', 'MT', 'ADDMATH'],
        'PHYSICS' => ['FIZIK', 'PHY'],
        'CHEMISTRY' => ['KIMIA', 'CHEM'],
        'BIOLOGY' => ['BIOLOGI', 'BIO'],
        'SAINS' => ['SCIENCE', 'SC'],
        'PRINSIP PERAKAUNAN' => ['PERAKAUNAN', 'PP', 'ACCOUNTING'],
        'EKONOMI' => ['ECONOMICS', 'ECO'],
        'PERDAGANGAN' => ['COMMERCE', 'PD'],
        'GEOGRAFI' => ['GEOGRAPHY', 'GEO'],
        'PENDIDIKAN SENI' => ['ART', 'PSV', 'SENI'],
        'REKA CIPTA' => ['RBT', 'REKA BENTUK'],
        'ASAS SAINS KOMPUTER' => ['ASK', 'COMPUTER SCIENCE', 'SAINS KOMPUTER'],
        'BAHASA ARAB' => ['ARAB'],
        'BAHASA CINA' => ['CINA', 'CHINESE'],
        'BAHASA TAMIL' => ['TAMIL'],
        'GRAFIK KOMUNIKASI TEKNIKAL' => ['GKT', 'TEKNIKAL'],
    ];

    /**
     * Upload and process SPM result slip
     */
    public function uploadSPM(Request $request)
    {
        $request->validate([
            'spm_file' => 'required|file|mimes:jpg,jpeg,png|max:5120'
        ]);

        try {
            $user = Auth::user();
            $file = $request->file('spm_file');

            // Generate unique filename
            $filename = 'spm_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('spm_documents', $filename, 'public');

            // Process OCR with enhanced accuracy
            $results = $this->processRealOCR($path);
            
            // Store in session with additional metadata
            Session::put('ocr_temp_data', [
                'file_path' => $path,
                'raw_grades' => $results['grades'],
                'grades' => $results['grades'],
                'total_as' => $results['total_as'],
                'total_subjects' => count($results['grades']),
                'detected_subjects' => array_keys($results['grades']),
                'timestamp' => now(),
                'confidence_score' => $results['confidence'] ?? 0.8,
                'raw_text' => $results['raw_text'] ?? ''
            ]);

            return response()->json([
                'success' => true,
                'grades' => $results['grades'],
                'totalAs' => $results['total_as'],
                'totalSubjects' => count($results['grades']),
                'detectedSubjects' => array_keys($results['grades']),
                'confidence' => $results['confidence'] ?? 0.8,
                'message' => 'SPM results extracted successfully!',
                'allowEdit' => true
            ]);

        } catch (\Exception $e) {
            Log::error('OCR Processing Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'file' => $request->file('spm_file')->getClientOriginalName()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process SPM: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process OCR with enhanced preprocessing and validation
     */
    private function processRealOCR($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        
        // Enhanced image preprocessing
        $processedPath = $this->enhancedImagePreprocessing($fullPath);
        
        // Multiple OCR attempts with different configurations
        $ocrResults = $this->performOCRWithFallback($processedPath);
        
        $text = $ocrResults['text'];
        $confidence = $ocrResults['confidence'];

        // Debug OCR output
        $this->debugOCROutput($text, $path);

        // Validate if it's a valid SPM result
        if (!$this->isValidSPMResult($text)) {
            throw new \Exception('Uploaded file is not a valid SPM result slip. Please ensure the image is clear and contains the SPM certificate.');
        }

        // Parse grades with enhanced algorithm
        $grades = $this->parseSPMGradesEnhanced($text);
        
        // Calculate confidence level
        $gradeConfidence = $this->calculateGradeConfidence($grades, $text);

        return [
            'grades' => $grades,
            'total_as' => $this->countAsFromGrades($grades),
            'confidence' => $gradeConfidence,
            'raw_text' => $text
        ];
    }

    /**
     * Enhanced image preprocessing with multiple techniques
     */
    private function enhancedImagePreprocessing($fullPath)
    {
        try {
            $imageInfo = getimagesize($fullPath);
            
            if (!$imageInfo) {
                throw new \Exception('Unable to read image file.');
            }

            // Load image based on type
            switch ($imageInfo['mime']) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fullPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($fullPath);
                    break;
                default:
                    throw new \Exception('Unsupported image format. Please use JPEG or PNG.');
            }

            if (!$image) {
                throw new \Exception('Failed to create image resource.');
            }

            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate optimal scaling (larger for better OCR)
            $scaleFactor = $this->calculateOptimalScale($width, $height);
            $newWidth = $width * $scaleFactor;
            $newHeight = $height * $scaleFactor;

            // Create resized image with high quality
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            
            if (!$resized) {
                throw new \Exception('Failed to create resized image.');
            }
            
            // Enable anti-aliasing
            imageantialias($resized, true);
            
            // Resample with high quality
            imagecopyresampled(
                $resized,
                $image,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );

            // Enhanced image processing chain
            $this->applyImageFilters($resized);

            // Save processed image
            $processedPath = storage_path('app/public/temp_' . uniqid() . '.jpg');
            imagejpeg($resized, $processedPath, 95);

            // Clean up memory
            imagedestroy($image);
            imagedestroy($resized);

            return $processedPath;

        } catch (\Exception $e) {
            Log::error('Image preprocessing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate optimal scaling factor based on image dimensions
     */
    private function calculateOptimalScale($width, $height)
    {
        $targetMinDimension = 1800;
        $currentMinDimension = min($width, $height);
        
        if ($currentMinDimension < $targetMinDimension) {
            return ceil($targetMinDimension / $currentMinDimension);
        }
        
        return 2; // Minimum 2x scaling for better OCR
    }

    /**
     * Apply chain of image filters for optimal OCR
     */
    private function applyImageFilters($image)
    {
        // Convert to grayscale
        imagefilter($image, IMG_FILTER_GRAYSCALE);
        
        // Increase contrast significantly
        imagefilter($image, IMG_FILTER_CONTRAST, -50);
        
        // Adjust brightness
        imagefilter($image, IMG_FILTER_BRIGHTNESS, 15);
        
        // Apply sharpening using convolution
        $this->applySharpening($image);
    }

    /**
     * Apply sharpening using convolution
     */
    private function applySharpening($image)
    {
        if (function_exists('imageconvolution')) {
            // Sharpening kernel
            $sharpenKernel = array(
                array(-1, -1, -1),
                array(-1, 16, -1),
                array(-1, -1, -1)
            );
            
            // Apply sharpening
            imageconvolution($image, $sharpenKernel, 8, 0);
            
            // Additional mild sharpening
            $mildSharpen = array(
                array(0, -1, 0),
                array(-1, 5, -1),
                array(0, -1, 0)
            );
            imageconvolution($image, $mildSharpen, 1, 0);
        } else {
            // Fallback: Use contrast enhancement as alternative
            imagefilter($image, IMG_FILTER_CONTRAST, -30);
            imagefilter($image, IMG_FILTER_BRIGHTNESS, 5);
        }
    }

    /**
     * Perform OCR with multiple configurations and fallback
     */
    private function performOCRWithFallback($processedPath)
    {
        $configurations = [
            // Primary: English + Digits + Whitelist
            [
                'lang' => 'eng',
                'psm' => 6, // Assume uniform block of text
                'oem' => 3, // Default + LSTM
                'whitelist' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-:;.,() []/'
            ],
            // Secondary: English + Malay
            [
                'lang' => 'eng+msa',
                'psm' => 6,
                'oem' => 3
            ],
            // Fallback: Default settings
            [
                'lang' => 'eng',
                'psm' => 3, // Fully automatic
                'oem' => 1 // Only LSTM
            ]
        ];

        $bestResult = null;
        $highestConfidence = 0;

        foreach ($configurations as $config) {
            try {
                $ocr = new TesseractOCR($processedPath);
                $ocr->executable('/usr/bin/tesseract');
                $ocr->lang($config['lang']);
                $ocr->psm($config['psm']);
                $ocr->oem($config['oem']);
                
                if (isset($config['whitelist'])) {
                    $ocr->whitelist($config['whitelist']);
                }
                
                $text = $ocr->run();
                
                // Calculate confidence based on text quality
                $confidence = $this->calculateOCRConfidence($text);
                
                if ($confidence > $highestConfidence) {
                    $highestConfidence = $confidence;
                    $bestResult = $text;
                }
                
                // If we have high confidence, break early
                if ($confidence > 0.8) {
                    break;
                }
                
            } catch (\Exception $e) {
                Log::warning('OCR configuration failed: ' . $e->getMessage());
                continue;
            }
        }

        if ($bestResult === null) {
            // Final fallback with basic settings
            try {
                $ocr = new TesseractOCR($processedPath);
                $ocr->executable('/usr/bin/tesseract');
                $bestResult = $ocr->run();
            } catch (\Exception $e) {
                throw new \Exception('Failed to perform OCR: ' . $e->getMessage());
            }
        }

        return [
            'text' => $bestResult,
            'confidence' => $highestConfidence
        ];
    }

    /**
     * Calculate confidence level of OCR result
     */
    private function calculateOCRConfidence($text)
    {
        $score = 0;
        $text = strtoupper($text);
        
        // Check for SPM keywords
        $keywords = ['SIJIL', 'PELAJARAN', 'LEMBAGA', 'PEPERIKSAAN', 'KEMENTERIAN', 'PENDIDIKAN'];
        $foundKeywords = 0;
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $foundKeywords++;
            }
        }
        $score += ($foundKeywords / count($keywords)) * 0.3;
        
        // Check for common subjects
        $allSubjectNames = array_keys(self::SUBJECTS);
        $foundSubjects = 0;
        foreach ($allSubjectNames as $subject) {
            if (strpos($text, $subject) !== false) {
                $foundSubjects++;
            }
        }
        $score += min(($foundSubjects / 10), 1) * 0.3;
        
        // Check for grade patterns
        preg_match_all('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $text, $matches);
        $gradeCount = count($matches[0]);
        $score += min(($gradeCount / 8), 1) * 0.3;
        
        // Check text length
        $textLength = strlen(preg_replace('/\s+/', '', $text));
        $score += min(($textLength / 500), 1) * 0.1;
        
        return min($score, 1);
    }

    /**
     * Validate if text contains SPM certificate markers
     */
    private function isValidSPMResult($text)
    {
        $text = strtoupper($text);
        
        $validationPatterns = [
            '/SIJIL\s*PELAJARAN/i',
            '/LEMBAGA\s*PEPERIKSAAN/i',
            '/KEMENTERIAN\s*PENDIDIKAN/i',
            '/SIJIL/i',
            '/PELAJARAN/i',
        ];
        
        $matches = 0;
        foreach ($validationPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $matches++;
            }
        }
        
        return $matches >= 2;
    }

    /**
     * Enhanced SPM grade parsing with better subject detection
     * Now only detects subjects that are actually in the certificate
     */
    private function parseSPMGradesEnhanced($text)
    {
        $text = strtoupper($text);
        
        // Apply OCR corrections
        $text = $this->applyOCRCorrections($text);
        
        // Split into lines and clean
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_filter(array_map('trim', $lines));
        
        $grades = [];
        $detectedSubjects = [];
        
        // Find the subject section in the certificate
        $subjectSection = $this->findSubjectSection($lines);
        
        if ($subjectSection) {
            // Parse subjects from the subject section only
            $grades = $this->parseSubjectsFromSection($subjectSection);
        } else {
            // Fallback: Parse from all lines but be more selective
            $grades = $this->parseSubjectsSelectively($lines);
        }
        
        // Validate and clean grades
        $grades = $this->validateAndCleanGrades($grades);
        
        if (count($grades) < 3) {
            throw new \Exception('Unable to detect enough subjects from SPM slip. Please ensure the image is clear.');
        }
        
        return $grades;
    }

    /**
     * Find the subject section in the certificate
     */
    private function findSubjectSection($lines)
    {
        $subjectSection = [];
        $inSubjectSection = false;
        $subjectKeywords = ['MATA PELAJARAN', 'SUBJECT', 'PELAJARAN', 'GRED', 'GRADE'];
        
        foreach ($lines as $index => $line) {
            // Look for the start of subject section
            foreach ($subjectKeywords as $keyword) {
                if (stripos($line, $keyword) !== false) {
                    $inSubjectSection = true;
                    // Skip the header line
                    continue 2;
                }
            }
            
            // If we're in the subject section, collect lines until we hit a line with no subject
            if ($inSubjectSection) {
                // Check if this line contains a subject or grade
                $hasSubject = false;
                foreach (array_keys(self::SUBJECTS) as $subject) {
                    if (stripos($line, $subject) !== false) {
                        $hasSubject = true;
                        break;
                    }
                }
                
                // Check if line has a grade
                $hasGrade = preg_match('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $line);
                
                if ($hasSubject || $hasGrade) {
                    $subjectSection[] = $line;
                } else {
                    // Check if this is a student info line (like name, IC, school)
                    $isStudentInfo = preg_match('/\d{6}-\d{2}-\d{4}/', $line) || // IC number
                                     preg_match('/\d{10}/', $line) || // Other numbers
                                     stripos($line, 'SMK') !== false ||
                                     stripos($line, 'SEKOLAH') !== false;
                    
                    if (!$isStudentInfo && !empty($line) && count($subjectSection) > 0) {
                        // We've likely left the subject section
                        break;
                    }
                }
            }
        }
        
        return $subjectSection;
    }

    /**
     * Parse subjects from the subject section
     */
    private function parseSubjectsFromSection($subjectSection)
    {
        $grades = [];
        
        foreach ($subjectSection as $line) {
            $line = preg_replace('/\s+/', ' ', $line);
            
            // Try to find a subject in this line
            foreach (self::SUBJECTS as $subject => $aliases) {
                $subjectFound = false;
                $allAliases = array_merge([$subject], $aliases);
                
                foreach ($allAliases as $alias) {
                    if (stripos($line, $alias) !== false) {
                        $subjectFound = true;
                        break;
                    }
                }
                
                if ($subjectFound && !isset($grades[$subject])) {
                    // Extract grade from this line
                    $grade = $this->extractGradeFromLine($line);
                    if ($grade) {
                        $grades[$subject] = $grade;
                        break;
                    }
                }
            }
        }
        
        return $grades;
    }

    /**
     * Parse subjects selectively (fallback method)
     */
    private function parseSubjectsSelectively($lines)
    {
        $grades = [];
        $allSubjectNames = array_keys(self::SUBJECTS);
        
        foreach ($lines as $line) {
            $line = preg_replace('/\s+/', ' ', $line);
            
            // Skip lines that are clearly not subject lines
            if (strlen($line) < 3 || 
                preg_match('/\d{6}-\d{2}-\d{4}/', $line) || // IC number
                preg_match('/^[0-9\s]+$/', $line) || // Only numbers
                stripos($line, 'SIJIL') !== false ||
                stripos($line, 'PELAJARAN') !== false ||
                stripos($line, 'LEMBAGA') !== false ||
                stripos($line, 'PEPERIKSAAN') !== false ||
                stripos($line, 'KEMENTERIAN') !== false ||
                stripos($line, 'PENGARAH') !== false ||
                stripos($line, 'DIRECTOR') !== false) {
                continue;
            }
            
            // Try to find a subject in this line
            foreach ($allSubjectNames as $subject) {
                if (stripos($line, $subject) !== false && !isset($grades[$subject])) {
                    // Extract grade
                    $grade = $this->extractGradeFromLine($line);
                    if ($grade) {
                        $grades[$subject] = $grade;
                        break;
                    }
                }
            }
        }
        
        return $grades;
    }

    /**
     * Apply OCR correction mappings
     */
    private function applyOCRCorrections($text)
    {
        foreach (self::OCR_CORRECTIONS as $wrong => $correct) {
            $text = str_replace($wrong, $correct, $text);
        }
        
        // Fix common spacing issues
        $text = preg_replace('/([A-Z])\s*\+\s*/', '$1+', $text);
        $text = preg_replace('/([A-Z])\s*\-\s*/', '$1-', $text);
        
        return $text;
    }

    /**
     * Extract grade from a line of text
     */
    private function extractGradeFromLine($line)
    {
        // Remove parenthetical grade descriptions
        $line = preg_replace('/\([^)]*\)/', '', $line);
        
        $gradePatterns = [
            '/\b(A\+)\b/i',
            '/\b(A-)\b/i',
            '/\b(A)\b(?![+-])/i',
            '/\b(B\+)\b/i',
            '/\b(B-)\b/i',
            '/\b(B)\b(?![+-])/i',
            '/\b(C\+)\b/i',
            '/\b(C-)\b/i',
            '/\b(C)\b(?![+-])/i',
            '/\b(D)\b/i',
            '/\b(E)\b/i',
            '/\b(G)\b/i'
        ];
        
        foreach ($gradePatterns as $pattern) {
            if (preg_match($pattern, $line, $matches)) {
                return strtoupper(trim($matches[1]));
            }
        }
        
        return null;
    }

    /**
     * Validate and clean grades array
     */
    private function validateAndCleanGrades($grades)
    {
        $validGrades = array_keys(self::GRADE_POINTS);
        $cleaned = [];
        
        foreach ($grades as $subject => $grade) {
            $grade = strtoupper(trim($grade));
            // Only keep if grade is valid and subject exists in our list
            if (in_array($grade, $validGrades) && isset(self::SUBJECTS[$subject])) {
                $cleaned[$subject] = $grade;
            }
        }
        
        return $cleaned;
    }

    /**
     * Calculate confidence level for extracted grades
     */
    private function calculateGradeConfidence($grades, $text)
    {
        $score = 0;
        $totalGrades = count($grades);
        
        if ($totalGrades === 0) {
            return 0;
        }
        
        // Check grade distribution (should be reasonable)
        $gradeCount = 0;
        foreach ($grades as $grade) {
            if (in_array($grade, ['A+', 'A', 'A-', 'B+', 'B', 'B-'])) {
                $gradeCount++;
            }
        }
        $score += ($gradeCount / $totalGrades) * 0.4;
        
        // Check text for subject validity
        $subjectCount = 0;
        foreach (array_keys(self::SUBJECTS) as $subject) {
            if (stripos($text, $subject) !== false) {
                $subjectCount++;
            }
        }
        $score += min(($subjectCount / 10), 1) * 0.3;
        
        // Check for SPM certificate markers
        $markerCount = 0;
        $markers = ['SIJIL', 'PELAJARAN', 'LEMBAGA', 'PEPERIKSAAN'];
        foreach ($markers as $marker) {
            if (stripos($text, $marker) !== false) {
                $markerCount++;
            }
        }
        $score += ($markerCount / count($markers)) * 0.3;
        
        return min($score, 1);
    }

    /**
     * Debug OCR output
     */
    private function debugOCROutput($text, $path)
    {
        try {
            $debugData = [
                'timestamp' => now()->toDateTimeString(),
                'file_path' => $path,
                'text_length' => strlen($text),
                'text_preview' => substr($text, 0, 500),
                'full_text' => $text,
                'grade_matches' => $this->extractAllGrades($text)
            ];
            
            $debugDir = storage_path('app/ocr_debug');
            if (!file_exists($debugDir)) {
                mkdir($debugDir, 0755, true);
            }
            
            $filename = 'ocr_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.json';
            file_put_contents(
                $debugDir . '/' . $filename,
                json_encode($debugData, JSON_PRETTY_PRINT)
            );
            
            // Keep only last 100 debug files
            $files = glob($debugDir . '/ocr_*.json');
            if (count($files) > 100) {
                usort($files, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                $toDelete = array_slice($files, 0, count($files) - 100);
                foreach ($toDelete as $file) {
                    unlink($file);
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('Failed to write OCR debug data: ' . $e->getMessage());
        }
    }

    /**
     * Extract all grades from text (for debugging)
     */
    private function extractAllGrades($text)
    {
        preg_match_all('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $text, $matches);
        return $matches[1] ?? [];
    }

    /**
     * Update OCR results with user edits
     */
    public function updateOCRResults(Request $request)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-,D,E,G'
        ]);

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'No OCR data found. Please upload your SPM results first.'
            ], 400);
        }

        $updatedGrades = [];
        $detectedSubjects = $tempData['detected_subjects'];

        foreach ($request->grades as $subject => $grade) {
            $subject = trim($subject);
            if (in_array($subject, $detectedSubjects)) {
                $updatedGrades[$subject] = $grade;
            }
        }

        // Keep any subjects that weren't updated
        foreach ($detectedSubjects as $subject) {
            if (!isset($updatedGrades[$subject])) {
                $updatedGrades[$subject] = $tempData['raw_grades'][$subject] ?? 'C';
            }
        }

        $totalAs = $this->countAsFromGrades($updatedGrades);
        
        $tempData['grades'] = $updatedGrades;
        $tempData['total_as'] = $totalAs;
        $tempData['user_edited'] = true;
        $tempData['updated_at'] = now();

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Grades updated successfully!',
            'totalAs' => $totalAs,
            'totalSubjects' => count($updatedGrades),
            'updatedGrades' => $updatedGrades
        ]);
    }

    /**
     * Verify and confirm OCR results
     */
    public function verifyOCRResults(Request $request)
    {
        $request->validate([
            'confirm' => 'required|boolean'
        ]);

        if (!$request->confirm) {
            // Clear session data
            $tempData = Session::get('ocr_temp_data');
            if ($tempData && isset($tempData['file_path'])) {
                Storage::disk('public')->delete($tempData['file_path']);
            }
            Session::forget('ocr_temp_data');

            return response()->json([
                'success' => true,
                'message' => 'OCR data cleared.',
                'redirect' => route('scholarship.finder')
            ]);
        }

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'No data to verify. Please upload your SPM results first.'
            ], 400);
        }

        // Store verified data permanently
        Session::put('verified_ocr_data', [
            'grades' => $tempData['grades'],
            'total_as' => $tempData['total_as'],
            'detected_subjects' => $tempData['detected_subjects'],
            'total_subjects' => count($tempData['grades']),
            'verified_at' => now(),
            'source' => $tempData['user_edited'] ?? false ? 'manual' : 'ocr'
        ]);

        // Clear temporary data
        Session::forget('ocr_temp_data');

        return response()->json([
            'success' => true,
            'message' => 'SPM results verified successfully!',
            'totalAs' => $tempData['total_as'],
            'totalSubjects' => count($tempData['grades'])
        ]);
    }

    /**
     * Add new subject manually
     */
    public function addSubject(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'grade' => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-,D,E,G'
        ]);

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'No OCR session found. Please upload your SPM results first.'
            ], 400);
        }

        $subject = trim($request->subject);
        
        // Check if subject already exists
        if (isset($tempData['grades'][$subject])) {
            return response()->json([
                'success' => false,
                'message' => 'Subject already exists. Please edit the existing entry.'
            ], 400);
        }

        // Add subject
        $tempData['grades'][$subject] = $request->grade;
        
        if (!in_array($subject, $tempData['detected_subjects'])) {
            $tempData['detected_subjects'][] = $subject;
        }

        $totalAs = $this->countAsFromGrades($tempData['grades']);
        $tempData['total_as'] = $totalAs;
        $tempData['user_edited'] = true;

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Subject added successfully!',
            'totalAs' => $totalAs,
            'totalSubjects' => count($tempData['grades'])
        ]);
    }

    /**
     * Remove subject
     */
    public function removeSubject(Request $request)
    {
        $request->validate([
            'subject' => 'required|string'
        ]);

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'No OCR session found.'
            ], 400);
        }

        $subject = trim($request->subject);

        if (!isset($tempData['grades'][$subject])) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found.'
            ], 404);
        }

        unset($tempData['grades'][$subject]);

        $tempData['detected_subjects'] = array_values(array_filter(
            $tempData['detected_subjects'],
            fn($s) => $s !== $subject
        ));

        $totalAs = $this->countAsFromGrades($tempData['grades']);
        $tempData['total_as'] = $totalAs;
        $tempData['user_edited'] = true;

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Subject removed successfully!',
            'totalAs' => $totalAs,
            'totalSubjects' => count($tempData['grades'])
        ]);
    }

    /**
     * Skip OCR and use manual entry
     */
    public function skipOCR()
    {
        Session::put('ocr_temp_data', [
            'file_path' => null,
            'raw_grades' => [],
            'grades' => [],
            'total_as' => 0,
            'total_subjects' => 0,
            'detected_subjects' => [],
            'manual_entry' => true,
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Manual entry mode activated.'
        ]);
    }

    /**
     * Get OCR session status
     */
    public function getOCRStatus()
    {
        $tempData = Session::get('ocr_temp_data');
        $verifiedData = Session::get('verified_ocr_data');

        return response()->json([
            'has_temp_data' => !is_null($tempData),
            'has_verified_data' => !is_null($verifiedData),
            'temp_data' => $tempData ? [
                'total_subjects' => count($tempData['grades'] ?? []),
                'total_as' => $tempData['total_as'] ?? 0,
                'timestamp' => $tempData['timestamp'] ?? null,
                'manual_entry' => $tempData['manual_entry'] ?? false
            ] : null,
            'verified_data' => $verifiedData ? [
                'total_subjects' => $verifiedData['total_subjects'] ?? 0,
                'total_as' => $verifiedData['total_as'] ?? 0,
                'verified_at' => $verifiedData['verified_at'] ?? null
            ] : null
        ]);
    }

    /**
     * Count total A's from grades (including A+, A, A-)
     */
    private function countAsFromGrades($grades)
    {
        if (!is_array($grades)) {
            return 0;
        }
        
        return collect($grades)->filter(function ($grade) {
            $grade = strtoupper(trim($grade));
            return str_starts_with($grade, 'A');
        })->count();
    }

    /**
     * Calculate grade points for ranking
     */
    private function getGradePoint($grade)
    {
        $grade = strtoupper(trim($grade));
        return self::GRADE_POINTS[$grade] ?? 0;
    }

    /**
     * Get grade distribution statistics
     */
    public function getGradeStatistics()
    {
        $verifiedData = Session::get('verified_ocr_data');
        
        if (!$verifiedData) {
            return response()->json([
                'success' => false,
                'message' => 'No verified data found.'
            ], 404);
        }

        $grades = $verifiedData['grades'];
        $stats = [
            'total' => count($grades),
            'total_as' => $this->countAsFromGrades($grades),
            'distributions' => [],
            'average_points' => 0
        ];

        $totalPoints = 0;
        foreach (self::GRADE_POINTS as $grade => $points) {
            $count = collect($grades)->filter(fn($g) => $g === $grade)->count();
            if ($count > 0) {
                $stats['distributions'][$grade] = $count;
                $totalPoints += $points * $count;
            }
        }

        $stats['average_points'] = $stats['total'] > 0 ? round($totalPoints / $stats['total'], 2) : 0;

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }
}