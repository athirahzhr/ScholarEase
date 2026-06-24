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
     * SPM Subjects - Only the ones that actually appear in SPM certificates
     */
    const SPM_SUBJECTS = [
        'BAHASA MELAYU',
        'BAHASA INGGERIS',
        'PENDIDIKAN ISLAM',
        'PENDIDIKAN MORAL',
        'SEJARAH',
        'MATHEMATICS',
        'ADDITIONAL MATHEMATICS',
        'PHYSICS',
        'CHEMISTRY',
        'BIOLOGY',
        'SAINS',
        'PRINSIP PERAKAUNAN',
        'EKONOMI',
        'PERDAGANGAN',
        'GEOGRAFI',
        'PENDIDIKAN SENI',
        'REKA CIPTA',
        'ASAS SAINS KOMPUTER',
        'BAHASA ARAB',
        'BAHASA CINA',
        'BAHASA TAMIL',
        'GRAFIK KOMUNIKASI TEKNIKAL',
    ];

    /**
     * Subject variations for OCR matching
     */
    const SUBJECT_VARIATIONS = [
        'BAHASA MELAYU' => ['BAHASA MELAYU', 'BM', 'MELAYU', 'B.MELAYU', 'BAHASA MALAYSIA'],
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
     * Grade corrections for common OCR misreadings
     */
    const GRADE_CORRECTIONS = [
        'AT' => 'A+',
        'AS' => 'A+',
        'A®' => 'A+',
        'A?' => 'A+',
        'A*' => 'A+',
        'A€' => 'A+',
        'A#' => 'A+',
        'A1' => 'A+',
        'BT' => 'B+',
        'BS' => 'B+',
        'B®' => 'B+',
        'B?' => 'B+',
        'B#' => 'B+',
        'CT' => 'C+',
        'CS' => 'C+',
        'C®' => 'C+',
        'C?' => 'C+',
        'C#' => 'C+',
        'A-' => 'A-',
        'B-' => 'B-',
        'C-' => 'C-',
        'A +' => 'A+',
        'B +' => 'B+',
        'C +' => 'C+',
        'A -' => 'A-',
        'B -' => 'B-',
        'C -' => 'C-',
        'A .' => 'A',
        'A .' => 'A',
        'A.' => 'A',
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

            $results = $this->processSPMOCR($path);
            
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
            Log::error('OCR Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process SPM: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process SPM OCR with specialized extraction
     */
    private function processSPMOCR($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            throw new \Exception('Image file not found. Please try uploading again.');
        }

        // Run OCR
        $text = $this->runSPMOCR($fullPath);
        
        if (empty($text)) {
            throw new \Exception('No text could be extracted from the image. Please ensure the image is clear and well-lit.');
        }

        // Debug output
        $this->debugOCROutput($text, $path);

        // Extract subjects and grades
        $grades = $this->extractSPMSubjectsAndGrades($text);
        
        if (empty($grades) || count($grades) < 3) {
            throw new \Exception('Unable to detect enough subjects from the SPM slip. Please try uploading a clearer image or use manual entry.');
        }

        $confidence = $this->calculateConfidence($grades, $text);

        return [
            'grades' => $grades,
            'total_as' => $this->countAsFromGrades($grades),
            'confidence' => $confidence,
            'raw_text' => $text
        ];
    }

    /**
     * Run OCR specifically for SPM certificates
     */
    private function runSPMOCR($imagePath)
    {
        // Try multiple configurations
        $configs = [
            ['lang' => 'eng+msa', 'psm' => 6, 'oem' => 3],
            ['lang' => 'eng+msa', 'psm' => 4, 'oem' => 3],
            ['lang' => 'eng', 'psm' => 6, 'oem' => 3],
            ['lang' => 'eng', 'psm' => 3, 'oem' => 1],
        ];

        $bestText = '';
        $bestScore = 0;

        foreach ($configs as $config) {
            try {
                $ocr = new TesseractOCR($imagePath);
                $ocr->lang($config['lang']);
                $ocr->psm($config['psm']);
                $ocr->oem($config['oem']);
                
                // Add character whitelist for better accuracy
                $ocr->whitelist('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-:;.,() []/');
                
                $text = $ocr->run();
                
                // Score this result
                $score = $this->scoreOCRText($text);
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestText = $text;
                }
                
                // If we have a good score, break early
                if ($score > 0.8) {
                    break;
                }
            } catch (\Exception $e) {
                Log::warning('OCR config failed: ' . $e->getMessage());
                continue;
            }
        }

        // If no good result, try without whitelist
        if (empty($bestText) || $bestScore < 0.3) {
            try {
                $ocr = new TesseractOCR($imagePath);
                $ocr->lang('eng+msa');
                $ocr->psm(6);
                $text = $ocr->run();
                
                if (!empty($text)) {
                    $bestText = $text;
                }
            } catch (\Exception $e) {
                Log::warning('Fallback OCR failed: ' . $e->getMessage());
            }
        }

        return $bestText;
    }

    /**
     * Score OCR text quality
     */
    private function scoreOCRText($text)
    {
        $score = 0;
        $text = strtoupper($text);
        
        // Check for SPM keywords (high importance)
        $keywords = ['SIJIL', 'PELAJARAN', 'LEMBAGA', 'PEPERIKSAAN', 'KEMENTERIAN', 'PENDIDIKAN'];
        $found = 0;
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $found++;
            }
        }
        $score += ($found / count($keywords)) * 0.5;
        
        // Check for common subjects
        $subjectCount = 0;
        foreach (self::SPM_SUBJECTS as $subject) {
            if (strpos($text, $subject) !== false) {
                $subjectCount++;
            }
        }
        $score += min($subjectCount / 9, 1) * 0.3;
        
        // Check for grades
        preg_match_all('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $text, $matches);
        $score += min(count($matches[0]) / 9, 1) * 0.2;
        
        return min($score, 1);
    }

    /**
     * Extract subjects and grades from SPM certificate text
     */
    private function extractSPMSubjectsAndGrades($text)
    {
        $text = strtoupper($text);
        
        // Apply grade corrections
        foreach (self::GRADE_CORRECTIONS as $wrong => $correct) {
            $text = str_replace($wrong, $correct, $text);
        }
        
        // Clean text
        $text = preg_replace('/\s+/', ' ', $text);
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        
        $grades = [];
        
        // Find the subject section
        $subjectSection = $this->findSubjectSection($lines);
        
        if (!empty($subjectSection)) {
            // Parse subjects from the section
            $grades = $this->parseSubjectSection($subjectSection);
        }
        
        // If we didn't get enough subjects, try parsing all lines
        if (count($grades) < 5) {
            $grades = $this->parseAllLines($lines);
        }
        
        // If still not enough, try to match from the entire text
        if (count($grades) < 5) {
            $grades = $this->parseFromText($text);
        }
        
        // Clean and validate
        $grades = $this->validateGrades($grades);
        
        return $grades;
    }

    /**
     * Find the subject section in the certificate
     */
    private function findSubjectSection($lines)
    {
        $section = [];
        $inSection = false;
        $foundSubjects = false;
        
        // Look for "MATA PELAJARAN" or "SUBJECT" header
        foreach ($lines as $index => $line) {
            if (strpos($line, 'MATA PELAJARAN') !== false || strpos($line, 'SUBJECT') !== false) {
                $inSection = true;
                continue;
            }
            
            if ($inSection) {
                // Check if this line has a subject
                $hasSubject = false;
                foreach (self::SPM_SUBJECTS as $subject) {
                    if (strpos($line, $subject) !== false) {
                        $hasSubject = true;
                        $foundSubjects = true;
                        break;
                    }
                }
                
                // Check if this line has a grade
                $hasGrade = preg_match('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $line);
                
                if ($hasSubject || $hasGrade) {
                    $section[] = $line;
                } elseif ($foundSubjects && (strlen($line) < 3 || preg_match('/^[0-9\s]+$/', $line))) {
                    // Stop if we've found subjects and hit a line with only numbers
                    break;
                }
            }
        }
        
        return $section;
    }

    /**
     * Parse subjects from the subject section
     */
    private function parseSubjectSection($lines)
    {
        $grades = [];
        
        foreach ($lines as $line) {
            // Try to find a subject in this line
            foreach (self::SUBJECT_VARIATIONS as $subject => $variations) {
                if (isset($grades[$subject])) {
                    continue;
                }
                
                $found = false;
                foreach ($variations as $variation) {
                    if (strpos($line, $variation) !== false) {
                        $found = true;
                        break;
                    }
                }
                
                if ($found) {
                    $grade = $this->extractGrade($line);
                    if ($grade) {
                        $grades[$subject] = $grade;
                    }
                    break;
                }
            }
        }
        
        return $grades;
    }

    /**
     * Parse all lines (fallback method)
     */
    private function parseAllLines($lines)
    {
        $grades = [];
        
        foreach ($lines as $line) {
            // Skip lines that are clearly not subjects
            if ($this->isNonSubjectLine($line)) {
                continue;
            }
            
            foreach (self::SUBJECT_VARIATIONS as $subject => $variations) {
                if (isset($grades[$subject])) {
                    continue;
                }
                
                foreach ($variations as $variation) {
                    if (strpos($line, $variation) !== false) {
                        $grade = $this->extractGrade($line);
                        if ($grade) {
                            $grades[$subject] = $grade;
                        }
                        break 2;
                    }
                }
            }
        }
        
        return $grades;
    }

    /**
     * Parse from text (last resort)
     */
    private function parseFromText($text)
    {
        $grades = [];
        
        // Extract all grades from text
        $allGrades = $this->extractAllGrades($text);
        
        // Find subjects that appear in the text
        $foundSubjects = [];
        foreach (self::SPM_SUBJECTS as $subject) {
            if (strpos($text, $subject) !== false) {
                $foundSubjects[] = $subject;
            }
        }
        
        // Map grades to subjects
        $gradeIndex = 0;
        foreach ($foundSubjects as $subject) {
            if ($gradeIndex < count($allGrades)) {
                $grades[$subject] = $allGrades[$gradeIndex];
                $gradeIndex++;
            }
        }
        
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
            '/TA[0-9]{3,}/', // Candidate number
        ];
        
        foreach ($skipPatterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        
        // Skip lines with only numbers or very short
        if (preg_match('/^[0-9\s]+$/', $line) || strlen(trim($line)) < 3) {
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
        
        // Check for A+ with spaces
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
            if (in_array($grade, $validGrades) && in_array($subject, self::SPM_SUBJECTS)) {
                $cleaned[$subject] = $grade;
            }
        }
        
        return $cleaned;
    }

    /**
     * Calculate confidence for grades
     */
    private function calculateConfidence($grades, $text)
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
                'found_subjects' => $this->findSubjectsInText($text)
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
        foreach (self::SPM_SUBJECTS as $subject) {
            if (strpos($text, $subject) !== false) {
                $found[] = $subject;
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