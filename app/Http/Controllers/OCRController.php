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
     * Allowed grade values
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
     * Subjects with their exact SPM certificate names
     */
    const SUBJECTS = [
        'BAHASA MELAYU' => ['BAHASA MELAYU', 'BM', 'MELAYU', 'B.MELAYU'],
        'BAHASA INGGERIS' => ['BAHASA INGGERIS', 'ENGLISH', 'BI', 'INGGERIS', 'B.INGGERIS'],
        'PENDIDIKAN ISLAM' => ['PENDIDIKAN ISLAM', 'PI', 'ISLAM', 'P.ISLAM'],
        'PENDIDIKAN MORAL' => ['PENDIDIKAN MORAL', 'PM', 'MORAL', 'P.MORAL'],
        'SEJARAH' => ['SEJARAH', 'SEJ', 'HISTORY'],
        'MATHEMATICS' => ['MATHEMATICS', 'MATEMATIK', 'MATH', 'MM'],
        'ADDITIONAL MATHEMATICS' => ['ADDITIONAL MATHEMATICS', 'MATEMATIK TAMBAHAN', 'ADD MATH', 'MT'],
        'PHYSICS' => ['PHYSICS', 'FIZIK', 'PHY'],
        'CHEMISTRY' => ['CHEMISTRY', 'KIMIA', 'CHEM'],
        'BIOLOGY' => ['BIOLOGY', 'BIOLOGI', 'BIO'],
        'SAINS' => ['SAINS', 'SCIENCE', 'SC'],
        'PRINSIP PERAKAUNAN' => ['PRINSIP PERAKAUNAN', 'PERAKAUNAN', 'ACCOUNTING', 'PP'],
        'EKONOMI' => ['EKONOMI', 'ECONOMICS', 'ECO'],
        'PERDAGANGAN' => ['PERDAGANGAN', 'COMMERCE', 'PD'],
        'GEOGRAFI' => ['GEOGRAFI', 'GEOGRAPHY', 'GEO'],
        'PENDIDIKAN SENI' => ['PENDIDIKAN SENI', 'SENI', 'ART', 'PSV'],
        'REKA CIPTA' => ['REKA CIPTA', 'RBT'],
        'ASAS SAINS KOMPUTER' => ['ASAS SAINS KOMPUTER', 'ASK', 'COMPUTER SCIENCE'],
        'BAHASA ARAB' => ['BAHASA ARAB', 'ARAB', 'B.ARAB'],
        'BAHASA CINA' => ['BAHASA CINA', 'CINA', 'CHINESE', 'B.CINA'],
        'BAHASA TAMIL' => ['BAHASA TAMIL', 'TAMIL', 'B.TAMIL'],
        'GRAFIK KOMUNIKASI TEKNIKAL' => ['GRAFIK KOMUNIKASI TEKNIKAL', 'GKT', 'TEKNIKAL', 'GRAFIK'],
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

            $filename = 'spm_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('spm_documents', $filename, 'public');

            $results = $this->processRealOCR($path);
            
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
            Log::error('OCR Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process SPM: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process OCR with extreme preprocessing
     */
    private function processRealOCR($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        
        // Create multiple preprocessed versions with extreme settings
        $processedPaths = $this->createExtremePreprocessedImages($fullPath);
        
        $bestText = '';
        $bestConfidence = 0;
        $allTexts = [];
        
        foreach ($processedPaths as $processedPath) {
            try {
                $text = $this->runOCR($processedPath);
                $allTexts[] = $text;
                
                // Clean up temp file
                if (file_exists($processedPath) && $processedPath !== $fullPath) {
                    unlink($processedPath);
                }
                
                // Check if this text contains SPM keywords
                if ($this->isValidSPMResult($text)) {
                    $confidence = $this->calculateOCRConfidence($text);
                    if ($confidence > $bestConfidence) {
                        $bestConfidence = $confidence;
                        $bestText = $text;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('OCR attempt failed: ' . $e->getMessage());
                continue;
            }
        }
        
        // If no text found with SPM keywords, try combining all texts
        if (empty($bestText)) {
            $combinedText = implode("\n", $allTexts);
            if ($this->isValidSPMResult($combinedText)) {
                $bestText = $combinedText;
                $bestConfidence = $this->calculateOCRConfidence($combinedText);
            }
        }

        if (empty($bestText)) {
            throw new \Exception('Failed to extract text from image. Please ensure the image is clear and try again.');
        }

        // Debug
        $this->debugOCROutput($bestText, $path);

        // Parse grades
        $grades = $this->parseGrades($bestText);
        
        // Calculate confidence
        $gradeConfidence = $this->calculateGradeConfidence($grades, $bestText);

        return [
            'grades' => $grades,
            'total_as' => $this->countAsFromGrades($grades),
            'confidence' => $gradeConfidence,
            'raw_text' => $bestText
        ];
    }

    /**
     * Create multiple extreme preprocessed versions
     */
    private function createExtremePreprocessedImages($fullPath)
    {
        $paths = [];
        $imageInfo = getimagesize($fullPath);
        
        if (!$imageInfo) {
            return [$fullPath];
        }

        // Different preprocessing combinations
        $configs = [
            ['scale' => 5, 'contrast' => -60, 'brightness' => 20, 'sharpness' => true],
            ['scale' => 4, 'contrast' => -70, 'brightness' => 15, 'sharpness' => true],
            ['scale' => 6, 'contrast' => -50, 'brightness' => 25, 'sharpness' => true],
            ['scale' => 4, 'contrast' => -80, 'brightness' => 10, 'sharpness' => false],
            ['scale' => 3, 'contrast' => -40, 'brightness' => 30, 'sharpness' => true],
        ];
        
        foreach ($configs as $config) {
            try {
                $processedPath = $this->preprocessImageExtreme($fullPath, $config);
                if ($processedPath && file_exists($processedPath)) {
                    $paths[] = $processedPath;
                }
            } catch (\Exception $e) {
                Log::warning('Extreme preprocessing failed: ' . $e->getMessage());
                continue;
            }
        }
        
        // Always include original
        $paths[] = $fullPath;
        
        return $paths;
    }

    /**
     * Extreme image preprocessing
     */
    private function preprocessImageExtreme($fullPath, $config)
    {
        $imageInfo = getimagesize($fullPath);
        if (!$imageInfo) {
            return null;
        }

        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($fullPath);
                break;
            default:
                return null;
        }

        if (!$image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Extreme scaling
        $scale = $config['scale'] ?? 5;
        $newWidth = $width * $scale;
        $newHeight = $height * $scale;

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imageantialias($resized, true);
        
        // Use high quality resampling
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Apply multiple filters
        imagefilter($resized, IMG_FILTER_GRAYSCALE);
        
        // Extreme contrast
        $contrast = $config['contrast'] ?? -60;
        imagefilter($resized, IMG_FILTER_CONTRAST, $contrast);
        
        // Brightness adjustment
        $brightness = $config['brightness'] ?? 20;
        imagefilter($resized, IMG_FILTER_BRIGHTNESS, $brightness);
        
        // Apply sharpening if enabled
        if (($config['sharpness'] ?? true) && function_exists('imageconvolution')) {
            // Multiple sharpening passes
            $sharpen1 = array(
                array(-1, -1, -1),
                array(-1, 24, -1),
                array(-1, -1, -1)
            );
            imageconvolution($resized, $sharpen1, 16, 0);
            
            $sharpen2 = array(
                array(0, -1, 0),
                array(-1, 5, -1),
                array(0, -1, 0)
            );
            imageconvolution($resized, $sharpen2, 1, 0);
        }
        
        // Additional filter for text enhancement
        imagefilter($resized, IMG_FILTER_CONTRAST, -30);

        // Save
        $tempPath = storage_path('app/public/temp_' . uniqid() . '.jpg');
        imagejpeg($resized, $tempPath, 100);

        imagedestroy($image);
        imagedestroy($resized);

        return $tempPath;
    }

    /**
     * Run OCR with multiple page segmentation modes
     */
    private function runOCR($imagePath)
    {
        $psmModes = [6, 4, 3, 7, 8]; // Different page segmentation modes
        
        $bestText = '';
        $bestScore = 0;
        
        foreach ($psmModes as $psm) {
            try {
                $ocr = new TesseractOCR($imagePath);
                $ocr->executable('/usr/bin/tesseract');
                $ocr->lang('eng+msa');
                $ocr->psm($psm);
                $ocr->oem(3);
                
                // Try with different character whitelists
                if ($psm === 6) {
                    $ocr->whitelist('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-:;.,() []/');
                }
                
                $text = $ocr->run();
                
                // Score this result
                $score = $this->scoreOCROutput($text);
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestText = $text;
                }
                
                // If we have a good result, stop
                if ($score > 0.7) {
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return $bestText;
    }

    /**
     * Score OCR output quality
     */
    private function scoreOCROutput($text)
    {
        $score = 0;
        $text = strtoupper($text);
        
        // Check for SPM keywords
        $keywords = ['SIJIL', 'PELAJARAN', 'LEMBAGA', 'PEPERIKSAAN'];
        $found = 0;
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $found++;
            }
        }
        $score += ($found / count($keywords)) * 0.5;
        
        // Check for subjects
        $subjectCount = 0;
        foreach (array_keys(self::SUBJECTS) as $subject) {
            if (strpos($text, $subject) !== false) {
                $subjectCount++;
            }
        }
        $score += min($subjectCount / 5, 1) * 0.3;
        
        // Check text length
        $textLength = strlen(preg_replace('/\s+/', '', $text));
        $score += min($textLength / 300, 1) * 0.2;
        
        return min($score, 1);
    }

    /**
     * Check if text is a valid SPM certificate
     */
    private function isValidSPMResult($text)
    {
        $text = strtoupper($text);
        $patterns = [
            '/SIJIL\s*PELAJARAN/',
            '/LEMBAGA\s*PEPERIKSAAN/',
            '/KEMENTERIAN\s*PENDIDIKAN/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Calculate OCR confidence
     */
    private function calculateOCRConfidence($text)
    {
        $score = 0;
        $text = strtoupper($text);
        
        // Check for SPM keywords
        $keywords = ['SIJIL', 'PELAJARAN', 'LEMBAGA', 'PEPERIKSAAN', 'KEMENTERIAN', 'PENDIDIKAN'];
        $found = 0;
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) $found++;
        }
        $score += ($found / count($keywords)) * 0.4;
        
        // Check for subjects
        $subjectCount = 0;
        foreach (array_keys(self::SUBJECTS) as $subject) {
            if (strpos($text, $subject) !== false) $subjectCount++;
        }
        $score += min($subjectCount / 9, 1) * 0.4;
        
        // Check for grades
        preg_match_all('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $text, $matches);
        $score += min(count($matches[0]) / 9, 1) * 0.2;
        
        return min($score, 1);
    }

    /**
     * Parse grades from OCR text
     */
    private function parseGrades($text)
    {
        $text = strtoupper($text);
        
        // Fix common OCR errors
        $fixes = [
            'AT' => 'A+', 'AS' => 'A+', 'A®' => 'A+', 'A?' => 'A+',
            'BT' => 'B+', 'BS' => 'B+', 'B®' => 'B+', 'B?' => 'B+',
            'CT' => 'C+', 'CS' => 'C+', 'C®' => 'C+', 'C?' => 'C+',
            'B-' => 'B-', 'C-' => 'C-', 'A-' => 'A-',
            'A .' => 'A', 'A .' => 'A',
        ];
        foreach ($fixes as $wrong => $correct) {
            $text = str_replace($wrong, $correct, $text);
        }
        
        // Clean text
        $text = preg_replace('/\s+/', ' ', $text);
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        
        $grades = [];
        
        // Method 1: Find subject-grade pairs
        foreach ($lines as $line) {
            if ($this->isNonSubjectLine($line)) {
                continue;
            }
            
            foreach (self::SUBJECTS as $subject => $aliases) {
                if (isset($grades[$subject])) {
                    continue;
                }
                
                foreach ($aliases as $alias) {
                    if (stripos($line, $alias) !== false) {
                        $grade = $this->extractGrade($line);
                        if ($grade) {
                            $grades[$subject] = $grade;
                        }
                        break 2;
                    }
                }
            }
        }
        
        // Method 2: Find grades in next lines if we're missing subjects
        if (count($grades) < 5) {
            foreach ($lines as $index => $line) {
                foreach (self::SUBJECTS as $subject => $aliases) {
                    if (isset($grades[$subject])) {
                        continue;
                    }
                    
                    foreach ($aliases as $alias) {
                        if (stripos($line, $alias) !== false) {
                            // Check next 3 lines for grade
                            for ($i = 1; $i <= 3; $i++) {
                                if (isset($lines[$index + $i])) {
                                    $grade = $this->extractGrade($lines[$index + $i]);
                                    if ($grade) {
                                        $grades[$subject] = $grade;
                                        break 3;
                                    }
                                }
                            }
                            break 2;
                        }
                    }
                }
            }
        }
        
        // Method 3: Extract all grades and map to common subjects
        if (count($grades) < 8) {
            $allGrades = $this->extractAllGrades($text);
            $commonSubjects = [
                'BAHASA MELAYU', 'BAHASA INGGERIS', 'PENDIDIKAN ISLAM',
                'SEJARAH', 'MATHEMATICS', 'ADDITIONAL MATHEMATICS',
                'PHYSICS', 'CHEMISTRY', 'GRAFIK KOMUNIKASI TEKNIKAL'
            ];
            
            $gradeIndex = 0;
            foreach ($commonSubjects as $subject) {
                if (!isset($grades[$subject]) && isset($allGrades[$gradeIndex])) {
                    // Check if subject appears in text
                    $found = false;
                    foreach (self::SUBJECTS[$subject] as $alias) {
                        if (stripos($text, $alias) !== false) {
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        $grades[$subject] = $allGrades[$gradeIndex];
                        $gradeIndex++;
                    }
                }
            }
        }
        
        // Clean and validate
        $grades = $this->validateGrades($grades);
        
        return $grades;
    }

    /**
     * Check if a line should be skipped
     */
    private function isNonSubjectLine($line)
    {
        $skipPatterns = [
            '/SIJIL/', '/PELAJARAN/', '/LEMBAGA/', '/PEPERIKSAAN/',
            '/KEMENTERIAN/', '/PENDIDIKAN/', '/MINISTRY/', '/EDUCATION/',
            '/PENGARAH/', '/DIRECTOR/', '/CALON/', '/CANDIDATE/',
            '/JUMLAH/', '/TAHUN/', '/GRED/', '/GRADE/',
            '/MATA PELAJARAN/', '/SUBJECT/',
            '/CEMERLANG/', '/TINGGI/', '/TERBAIK/',
            '/[0-9]{6}-[0-9]{2}-[0-9]{4}/', // IC
            '/SMK/', '/SEKOLAH/', '/SCHOOL/',
            '/WAN/', '/BINTI/', '/BIN/', // Malay names
            '/[0-9]{8,}/', // Long numbers
            '/^[0-9\s]+$/', // Only numbers
        ];
        
        foreach ($skipPatterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        
        // Skip short lines
        if (strlen(trim($line)) < 3) {
            return true;
        }
        
        return false;
    }

    /**
     * Extract grade from a line
     */
    private function extractGrade($line)
    {
        // Remove parentheses content
        $line = preg_replace('/\([^)]*\)/', '', $line);
        
        // Check for A+ with possible spaces
        if (preg_match('/\b(A\s*\+)\b/i', $line)) {
            return 'A+';
        }
        
        // Check for A-
        if (preg_match('/\b(A\s*-)\b/i', $line)) {
            return 'A-';
        }
        
        // Check for other grades
        $patterns = [
            '/\b(A\+)\b/i' => 'A+',
            '/\b(A-)\b/i' => 'A-',
            '/\b(A)\b(?![+-])/i' => 'A',
            '/\b(B\+)\b/i' => 'B+',
            '/\b(B-)\b/i' => 'B-',
            '/\b(B)\b(?![+-])/i' => 'B',
            '/\b(C\+)\b/i' => 'C+',
            '/\b(C-)\b/i' => 'C-',
            '/\b(C)\b(?![+-])/i' => 'C',
            '/\b(D)\b/i' => 'D',
            '/\b(E)\b/i' => 'E',
            '/\b(G)\b/i' => 'G'
        ];
        
        foreach ($patterns as $pattern => $grade) {
            if (preg_match($pattern, $line)) {
                return $grade;
            }
        }
        
        return null;
    }

    /**
     * Extract all grades from text
     */
    private function extractAllGrades($text)
    {
        preg_match_all('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $text, $matches);
        $grades = $matches[1] ?? [];
        
        // Also check for spaced grades
        preg_match_all('/\b(A\s*\+)\b/i', $text, $matches2);
        if (!empty($matches2[1])) {
            $grades = array_merge($grades, array_map(function($g) { return 'A+'; }, $matches2[1]));
        }
        
        preg_match_all('/\b(A\s*-)\b/i', $text, $matches3);
        if (!empty($matches3[1])) {
            $grades = array_merge($grades, array_map(function($g) { return 'A-'; }, $matches3[1]));
        }
        
        // Clean and deduplicate
        $grades = array_map(function($g) {
            return strtoupper(str_replace(' ', '', $g));
        }, $grades);
        
        return array_values(array_unique($grades));
    }

    /**
     * Validate and clean grades
     */
    private function validateGrades($grades)
    {
        $validGrades = array_keys(self::GRADE_POINTS);
        $cleaned = [];
        
        foreach ($grades as $subject => $grade) {
            $grade = strtoupper(trim(str_replace(' ', '', $grade)));
            if (in_array($grade, $validGrades) && isset(self::SUBJECTS[$subject])) {
                $cleaned[$subject] = $grade;
            }
        }
        
        return $cleaned;
    }

    /**
     * Calculate confidence for grades
     */
    private function calculateGradeConfidence($grades, $text)
    {
        $score = 0;
        $total = count($grades);
        
        if ($total === 0) {
            return 0;
        }
        
        // How many subjects we found (aiming for 9)
        $score += min($total / 9, 1) * 0.5;
        
        // Check if we found common subjects
        $common = ['BAHASA MELAYU', 'BAHASA INGGERIS', 'SEJARAH', 'MATHEMATICS'];
        $foundCommon = 0;
        foreach ($common as $subject) {
            if (isset($grades[$subject])) {
                $foundCommon++;
            }
        }
        $score += ($foundCommon / count($common)) * 0.3;
        
        // Check for SPM markers
        $markers = ['SIJIL', 'PELAJARAN', 'LEMBAGA'];
        $foundMarkers = 0;
        foreach ($markers as $marker) {
            if (stripos($text, $marker) !== false) {
                $foundMarkers++;
            }
        }
        $score += ($foundMarkers / count($markers)) * 0.2;
        
        return min($score, 1);
    }

    /**
     * Count A's from grades
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
     * Debug OCR output
     */
    private function debugOCROutput($text, $path)
    {
        try {
            $debugData = [
                'timestamp' => now()->toDateTimeString(),
                'file_path' => $path,
                'text_length' => strlen($text),
                'text_preview' => substr($text, 0, 1000),
                'full_text' => $text,
                'extracted_grades' => $this->extractAllGrades($text),
                'detected_subjects' => $this->findSubjectsInText($text)
            ];
            
            $debugDir = storage_path('app/ocr_debug');
            if (!file_exists($debugDir)) {
                mkdir($debugDir, 0755, true);
            }
            
            $filename = 'ocr_' . date('Y-m-d_H-i-s') . '.json';
            file_put_contents(
                $debugDir . '/' . $filename,
                json_encode($debugData, JSON_PRETTY_PRINT)
            );
        } catch (\Exception $e) {
            Log::warning('Debug write failed: ' . $e->getMessage());
        }
    }

    /**
     * Find subjects in text (for debugging)
     */
    private function findSubjectsInText($text)
    {
        $found = [];
        foreach (self::SUBJECTS as $subject => $aliases) {
            foreach ($aliases as $alias) {
                if (stripos($text, $alias) !== false) {
                    $found[] = $subject;
                    break;
                }
            }
        }
        return $found;
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
                'message' => 'No OCR data found.'
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
     * Verify OCR results
     */
    public function verifyOCRResults(Request $request)
    {
        $request->validate([
            'confirm' => 'required|boolean'
        ]);

        if (!$request->confirm) {
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
                'message' => 'No data to verify.'
            ], 400);
        }

        Session::put('verified_ocr_data', [
            'grades' => $tempData['grades'],
            'total_as' => $tempData['total_as'],
            'detected_subjects' => $tempData['detected_subjects'],
            'total_subjects' => count($tempData['grades']),
            'verified_at' => now(),
            'source' => $tempData['user_edited'] ?? false ? 'manual' : 'ocr'
        ]);

        Session::forget('ocr_temp_data');

        return response()->json([
            'success' => true,
            'message' => 'SPM results verified successfully!',
            'totalAs' => $tempData['total_as'],
            'totalSubjects' => count($tempData['grades'])
        ]);
    }

    /**
     * Add subject manually
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
                'message' => 'No OCR session found.'
            ], 400);
        }

        $subject = trim($request->subject);
        
        if (isset($tempData['grades'][$subject])) {
            return response()->json([
                'success' => false,
                'message' => 'Subject already exists.'
            ], 400);
        }

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
     * Skip OCR
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
     * Get OCR status
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
     * Get grade statistics
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