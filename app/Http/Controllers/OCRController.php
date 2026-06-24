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
     * SPM Subjects mapping
     */
    const SUBJECT_MAPPING = [
        'BAHASA MELAYU' => ['BAHASA MELAYU', 'BM', 'MELAYU'],
        'BAHASA INGGERIS' => ['BAHASA INGGERIS', 'ENGLISH', 'BI', 'INGGERIS'],
        'PENDIDIKAN ISLAM' => ['PENDIDIKAN ISLAM', 'PI', 'ISLAM', 'PENDIDIRAN ISLAM'],
        'PENDIDIKAN MORAL' => ['PENDIDIKAN MORAL', 'PM', 'MORAL'],
        'SEJARAH' => ['SEJARAH', 'SEJ', 'HISTORY', 'SEIARAH'],
        'MATHEMATICS' => ['MATHEMATICS', 'MATEMATIK', 'MATH'],
        'ADDITIONAL MATHEMATICS' => ['ADDITIONAL MATHEMATICS', 'MATEMATIK TAMBAHAN', 'ADD MATH'],
        'PHYSICS' => ['PHYSICS', 'FIZIK'],
        'CHEMISTRY' => ['CHEMISTRY', 'KIMIA'],
        'BIOLOGY' => ['BIOLOGY', 'BIOLOGI'],
        'SAINS' => ['SAINS', 'SCIENCE'],
        'PRINSIP PERAKAUNAN' => ['PRINSIP PERAKAUNAN', 'PERAKAUNAN', 'ACCOUNTING'],
        'EKONOMI' => ['EKONOMI', 'ECONOMICS'],
        'PERDAGANGAN' => ['PERDAGANGAN', 'COMMERCE'],
        'GEOGRAFI' => ['GEOGRAFI', 'GEOGRAPHY'],
        'PENDIDIKAN SENI' => ['PENDIDIKAN SENI', 'SENI', 'ART'],
        'REKA CIPTA' => ['REKA CIPTA', 'RBT'],
        'ASAS SAINS KOMPUTER' => ['ASAS SAINS KOMPUTER', 'ASK', 'COMPUTER SCIENCE'],
        'BAHASA ARAB' => ['BAHASA ARAB', 'ARAB'],
        'BAHASA CINA' => ['BAHASA CINA', 'CINA', 'CHINESE'],
        'BAHASA TAMIL' => ['BAHASA TAMIL', 'TAMIL'],
        'GRAFIK KOMUNIKASI TEKNIKAL' => ['GRAFIK KOMUNIKASI TEKNIKAL', 'GKT', 'TEKNIKAL', 'GRAFIK', 'TEKNIRAL'],
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
     * Process SPM OCR
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

        // Debug - save the OCR output
        $this->debugOCROutput($text, $path);
        
        Log::info('=== OCR RAW TEXT ===');
        Log::info($text);
        Log::info('=== END OCR RAW TEXT ===');

        // Extract subjects and grades
        $grades = $this->extractGradesFromOCRText($text);
        
        Log::info('=== EXTRACTED GRADES ===');
        Log::info(json_encode($grades));
        Log::info('=== END EXTRACTED GRADES ===');
        
        if (empty($grades) || count($grades) < 3) {
            throw new \Exception('Unable to detect enough subjects from the SPM slip. Found ' . count($grades) . ' subjects.');
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
     * Run OCR with PSM 6
     */
    private function runSPMOCR($imagePath)
    {
        try {
            $ocr = new TesseractOCR($imagePath);
            $ocr->executable('/usr/bin/tesseract');
            $ocr->lang('eng+msa');
            $ocr->psm(6);
            $ocr->oem(3);
            
            return $ocr->run();
            
        } catch (\Exception $e) {
            Log::error('OCR Execution Error: ' . $e->getMessage());
            
            try {
                $ocr = new TesseractOCR($imagePath);
                $ocr->executable('/usr/bin/tesseract');
                $ocr->lang('eng+msa');
                return $ocr->run();
            } catch (\Exception $e2) {
                Log::error('OCR Fallback Error: ' . $e2->getMessage());
                return '';
            }
        }
    }

    /**
     * Extract grades from OCR text - FIXED VERSION
     * This version correctly extracts grades like A+, A-, A from the text
     */
    private function extractGradesFromOCRText($text)
    {
        // Clean the text
        $text = str_replace("\r", "\n", $text);
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        
        $grades = [];
        
        Log::info('Processing ' . count($lines) . ' lines');
        
        // Process each line
        foreach ($lines as $line) {
            // Skip empty lines
            if (empty($line)) {
                continue;
            }
            
            // Skip header/footer lines
            if ($this->isNonSubjectLine($line)) {
                continue;
            }
            
            // Try to extract subject and grade from this line
            $result = $this->extractSubjectAndGradeFromLine($line);
            
            if ($result) {
                $subject = $result['subject'];
                $grade = $result['grade'];
                
                // Match to standard subject name
                $standardSubject = $this->matchSubject($subject);
                
                if ($standardSubject && !isset($grades[$standardSubject])) {
                    $grades[$standardSubject] = $grade;
                    Log::info("Added: $standardSubject -> $grade (from: $subject)");
                }
            }
        }
        
        // If we still don't have enough subjects, try a different approach
        if (count($grades) < 5) {
            Log::info('Trying alternative extraction method...');
            $altGrades = $this->extractUsingRegex($text);
            foreach ($altGrades as $subject => $grade) {
                if (!isset($grades[$subject])) {
                    $grades[$subject] = $grade;
                    Log::info("Added from regex: $subject -> $grade");
                }
            }
        }
        
        // Validate and clean grades
        $grades = $this->validateGrades($grades);
        
        return $grades;
    }

    /**
     * Extract subject and grade from a single line - FIXED VERSION
     * Now correctly handles A+, A-, A with parentheses
     */
    private function extractSubjectAndGradeFromLine($line)
    {
        // Log the original line
        Log::info("Processing line: " . $line);
        
        // Remove parenthetical content like (CEMERLANG TERTINGGI) but keep the grade
        $lineClean = preg_replace('/\([^)]*\)/', '', $line);
        $lineClean = trim($lineClean);
        
        Log::info("Cleaned line: " . $lineClean);
        
        if (empty($lineClean)) {
            return null;
        }
        
        // PATTERN 1: Subject followed by grade at the end
        // Handles: "BAHASA MELAYU A+", "BAHASA INGGERIS A-", "ADDITIONAL MATHEMATICS A+"
        $pattern1 = '/^(.*?)\s+(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)$/i';
        if (preg_match($pattern1, $lineClean, $matches)) {
            $subject = trim($matches[1]);
            $grade = strtoupper(trim($matches[2]));
            Log::info("Pattern 1 matched: subject=$subject, grade=$grade");
            
            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }
        
        // PATTERN 2: Subject followed by grade with possible text in between
        // Handles: "BAHASA MELAYU A+ (CEMERLANG TERTINGGI)" - but parentheses already removed
        $pattern2 = '/^(.*?)\s+(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\s*$/i';
        if (preg_match($pattern2, $lineClean, $matches)) {
            $subject = trim($matches[1]);
            $grade = strtoupper(trim($matches[2]));
            Log::info("Pattern 2 matched: subject=$subject, grade=$grade");
            
            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }
        
        // PATTERN 3: Find any known subject in the line and extract grade after it
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                $pos = stripos($lineClean, $variation);
                if ($pos !== false) {
                    // Extract everything after the subject
                    $afterSubject = trim(substr($lineClean, $pos + strlen($variation)));
                    Log::info("After subject '$variation': " . $afterSubject);
                    
                    // Try to find a grade in the remaining text
                    $gradePattern = '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/i';
                    if (preg_match($gradePattern, $afterSubject, $gradeMatches)) {
                        $grade = strtoupper(trim($gradeMatches[1]));
                        Log::info("Pattern 3 matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }
                    
                    // If no grade found in the remaining text, check if the grade is just a single character
                    // Sometimes OCR puts grade as just "A" or "A+" without space
                    if (preg_match('/\s+(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)$/i', $afterSubject, $gradeMatches)) {
                        $grade = strtoupper(trim($gradeMatches[1]));
                        Log::info("Pattern 3b matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }
                    
                    break 2;
                }
            }
        }
        
        // PATTERN 4: Try to find grade pattern in the entire line (including parentheses)
        // This handles cases where grade is inside parentheses like "A+ (CEMERLANG TERTINGGI)"
        $gradeInParentheses = '/\((A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\s*[^)]*\)/i';
        if (preg_match($gradeInParentheses, $line, $matches)) {
            $grade = strtoupper(trim($matches[1]));
            
            // Try to find subject before the parentheses
            $beforeParentheses = trim(substr($line, 0, strpos($line, '(')));
            
            // Find subject in the text before parentheses
            foreach (self::SUBJECT_MAPPING as $standard => $variations) {
                foreach ($variations as $variation) {
                    if (stripos($beforeParentheses, $variation) !== false) {
                        Log::info("Pattern 4 matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }
                }
            }
        }
        
        // PATTERN 5: Check if the line contains a known subject and grade is somewhere in the line
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                if (stripos($line, $variation) !== false) {
                    // Look for grade anywhere in the line
                    $gradePattern = '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/i';
                    if (preg_match($gradePattern, $line, $gradeMatches)) {
                        $grade = strtoupper(trim($gradeMatches[1]));
                        Log::info("Pattern 5 matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }
                    break 2;
                }
            }
        }
        
        return null;
    }

    /**
     * Extract using regex on the entire text - FIXED VERSION
     */
    private function extractUsingRegex($text)
    {
        $grades = [];
        
        // Pattern: Subject name followed by grade
        // This handles the exact format from the OCR output
        $pattern = '/(BAHASA MELAYU|BAHASA INGGERIS|PENDIDIKAN ISLAM|SEJARAH|MATHEMATICS|ADDITIONAL MATHEMATICS|GRAFIK KOMUNIKASI TEKNIKAL|PHYSICS|CHEMISTRY)\s*(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)/i';
        
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $subject = trim($match[1]);
                $grade = strtoupper(trim($match[2]));
                
                $standardSubject = $this->matchSubject($subject);
                if ($standardSubject && !isset($grades[$standardSubject])) {
                    $grades[$standardSubject] = $grade;
                    Log::info("Regex matched: $standardSubject -> $grade");
                }
            }
        }
        
        // If the above pattern didn't work, try with more flexible matching
        if (empty($grades)) {
            // Look for subject variations with grade in parentheses
            foreach (self::SUBJECT_MAPPING as $standard => $variations) {
                foreach ($variations as $variation) {
                    $pattern2 = '/' . preg_quote($variation, '/') . '.*?\((A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)/i';
                    if (preg_match($pattern2, $text, $matches)) {
                        $grade = strtoupper(trim($matches[1]));
                        if (!isset($grades[$standard])) {
                            $grades[$standard] = $grade;
                            Log::info("Regex variation matched: $standard -> $grade");
                        }
                        break;
                    }
                }
            }
        }
        
        return $grades;
    }

    /**
     * Match subject to standard name
     */
    private function matchSubject($subject)
    {
        $subject = strtoupper(trim($subject));
        
        // Direct match
        if (isset(self::SUBJECT_MAPPING[$subject])) {
            return $subject;
        }
        
        // Check variations
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                $variation = strtoupper($variation);
                if ($subject === $variation || strpos($subject, $variation) !== false) {
                    return $standard;
                }
            }
        }
        
        // Handle OCR misreadings
        $fuzzyMatches = [
            'PENDIDIRAN' => 'PENDIDIKAN',
            'SEIARAH' => 'SEJARAH',
            'TEKNIRAL' => 'TEKNIKAL',
            'TERTINGOD' => 'TERTINGGI',
            'FELAJARAN' => 'PELAJARAN',
        ];
        
        foreach ($fuzzyMatches as $wrong => $correct) {
            if (strpos($subject, $wrong) !== false) {
                $subject = str_ireplace($wrong, $correct, $subject);
                // Try to match again
                foreach (self::SUBJECT_MAPPING as $standard => $variations) {
                    foreach ($variations as $variation) {
                        $variation = strtoupper($variation);
                        if (strpos($subject, $variation) !== false) {
                            return $standard;
                        }
                    }
                }
            }
        }
        
        return null;
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
            '/^[0-9\s]+$/', // Only numbers
            '/^[A-Z\s]{0,5}$/', // Very short
            '/PEPERIKSAAN TAHUN/', // Year
            '/Pengarah Peperiksaan/', // Director
            '/Director of Examinations/', // Director English
            '/©/', // Copyright symbol
            '/^[A-Z\s]{0,3}$/', // Very short words
            '/Bebe Ministry/', // OCR misread
            '/Kementerian Pendidikan Malaysia/', // Ministry
            '/A 05458270/', // Certificate number
            '/201381 159/', // Certificate number
        ];
        
        foreach ($skipPatterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        
        // Skip very short lines
        if (strlen(trim($line)) < 3) {
            return true;
        }
        
        return false;
    }

    /**
     * Validate and clean grades
     */
    private function validateGrades($grades)
    {
        $validGrades = ['A+', 'A-', 'A', 'B+', 'B-', 'B', 'C+', 'C-', 'C', 'D', 'E', 'G'];
        $cleaned = [];
        
        foreach ($grades as $subject => $grade) {
            $grade = strtoupper(trim($grade));
            if (in_array($grade, $validGrades) && isset(self::SUBJECT_MAPPING[$subject])) {
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
            $debugDir = storage_path('app/ocr_debug');
            if (!file_exists($debugDir)) {
                mkdir($debugDir, 0755, true);
            }
            
            $filename = 'ocr_' . date('Y-m-d_H-i-s') . '.txt';
            file_put_contents(
                $debugDir . '/' . $filename,
                $text
            );
        } catch (\Exception $e) {
            Log::warning('Debug write failed: ' . $e->getMessage());
        }
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