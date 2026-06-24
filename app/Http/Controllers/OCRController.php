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
     * SPM Subjects mapping with variations including OCR misreadings
     */
    const SUBJECT_MAPPING = [
        'BAHASA MELAYU' => ['BAHASA MELAYU', 'BM', 'MELAYU', 'B.MELAYU'],
        'BAHASA INGGERIS' => ['BAHASA INGGERIS', 'ENGLISH', 'BI', 'INGGERIS', 'B.INGGERIS'],
        'PENDIDIKAN ISLAM' => ['PENDIDIKAN ISLAM', 'PI', 'ISLAM', 'P.ISLAM', 'PENDIDIRAN ISLAM'], // OCR misread
        'PENDIDIKAN MORAL' => ['PENDIDIKAN MORAL', 'PM', 'MORAL', 'P.MORAL'],
        'SEJARAH' => ['SEJARAH', 'SEJ', 'HISTORY', 'SEIARAH'], // OCR misread
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
        'GRAFIK KOMUNIKASI TEKNIKAL' => ['GRAFIK KOMUNIKASI TEKNIKAL', 'GKT', 'TEKNIKAL', 'GRAFIK', 'TEKNIRAL'], // OCR misread
    ];

    /**
     * Grade corrections for OCR misreadings
     */
    const GRADE_CORRECTIONS = [
        // From the OCR debug output
        'As' => 'A-',
        'Ae' => 'A-',
        'AY' => 'A+',
        'A+' => 'A+',
        'A' => 'A',
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
        
        // Log the text for debugging
        Log::info('OCR Text: ' . $text);

        // Extract subjects and grades
        $grades = $this->extractGradesFromOCRText($text);
        
        // Log what we found
        Log::info('Extracted grades: ' . json_encode($grades));
        
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
     * Extract grades from OCR text - Simplified and more robust
     */
    private function extractGradesFromOCRText($text)
    {
        // Clean the text
        $text = str_replace("\r", "\n", $text);
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        
        $grades = [];
        
        // Log all lines for debugging
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
            
            // Try to extract subject and grade using multiple patterns
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
        
        // If we still don't have enough subjects, try looking for pattern where subject and grade are on separate lines
        if (count($grades) < 5) {
            Log::info('Trying separate line detection...');
            $additionalGrades = $this->extractFromSeparateLines($lines);
            foreach ($additionalGrades as $subject => $grade) {
                if (!isset($grades[$subject])) {
                    $grades[$subject] = $grade;
                    Log::info("Added from separate lines: $subject -> $grade");
                }
            }
        }
        
        // If still not enough, try to extract from the raw text
        if (count($grades) < 5) {
            Log::info('Trying raw text extraction...');
            $rawGrades = $this->extractFromRawText($text);
            foreach ($rawGrades as $subject => $grade) {
                if (!isset($grades[$subject])) {
                    $grades[$subject] = $grade;
                    Log::info("Added from raw text: $subject -> $grade");
                }
            }
        }
        
        // Validate and clean grades
        $grades = $this->validateGrades($grades);
        
        Log::info('Final grades: ' . json_encode($grades));
        
        return $grades;
    }

    /**
     * Extract subject and grade from a single line - More flexible pattern matching
     */
    private function extractSubjectAndGradeFromLine($line)
    {
        // Remove parenthetical content like (CEMERLANG TERTINGGI)
        $lineWithoutParentheses = preg_replace('/\([^)]*\)/', '', $line);
        $lineWithoutParentheses = trim($lineWithoutParentheses);
        
        // If after removing parentheses, the line is empty, skip
        if (empty($lineWithoutParentheses)) {
            return null;
        }
        
        // Pattern 1: Subject followed by grade at the end
        // This handles: "BAHASA MELAYU A+" or "BAHASA INGGERIS As"
        $pattern1 = '/^(.*?)\s+(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)\s*$/i';
        if (preg_match($pattern1, $lineWithoutParentheses, $matches)) {
            $subject = trim($matches[1]);
            $grade = $this->correctGrade(trim($matches[2]));
            
            // Make sure subject is not empty and grade is valid
            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }
        
        // Pattern 2: Grade followed by subject (less common)
        $pattern2 = '/^\s*(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)\s+(.*)$/i';
        if (preg_match($pattern2, $lineWithoutParentheses, $matches)) {
            $grade = $this->correctGrade(trim($matches[1]));
            $subject = trim($matches[2]);
            
            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }
        
        // Pattern 3: Try to find any grade in the line and extract subject before it
        $gradePattern = '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)\b/i';
        if (preg_match($gradePattern, $lineWithoutParentheses, $gradeMatches, PREG_OFFSET_CAPTURE)) {
            $grade = $this->correctGrade($gradeMatches[1][0]);
            $gradePos = $gradeMatches[1][1];
            $subject = trim(substr($lineWithoutParentheses, 0, $gradePos));
            
            // Check if this line contains a known subject
            $knownSubject = $this->findKnownSubject($subject);
            if ($knownSubject && !empty($grade)) {
                return ['subject' => $knownSubject, 'grade' => $grade];
            }
            
            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }
        
        // Pattern 4: Check if this line contains a known subject and look for grade at the end
        $knownSubject = $this->findKnownSubject($lineWithoutParentheses);
        if ($knownSubject) {
            // Try to extract grade from the rest of the line
            $restOfLine = str_ireplace($knownSubject, '', $lineWithoutParentheses);
            $restOfLine = trim($restOfLine);
            
            // Check if rest of line has a grade
            $gradePattern2 = '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)\b/i';
            if (preg_match($gradePattern2, $restOfLine, $gradeMatches)) {
                $grade = $this->correctGrade($gradeMatches[1]);
                return ['subject' => $knownSubject, 'grade' => $grade];
            }
        }
        
        return null;
    }

    /**
     * Find a known subject in a line
     */
    private function findKnownSubject($line)
    {
        $line = strtoupper(trim($line));
        
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                $variation = strtoupper($variation);
                if (strpos($line, $variation) !== false) {
                    return $standard;
                }
            }
        }
        
        return null;
    }

    /**
     * Extract from separate lines (subject on one line, grade on next)
     */
    private function extractFromSeparateLines($lines)
    {
        $grades = [];
        
        for ($i = 0; $i < count($lines) - 1; $i++) {
            $line1 = $lines[$i];
            $line2 = $lines[$i + 1];
            
            // Skip if lines are non-subject
            if ($this->isNonSubjectLine($line1) || $this->isNonSubjectLine($line2)) {
                continue;
            }
            
            // Check if line1 has a known subject
            $subject = $this->findKnownSubject($line1);
            
            // Check if line2 has a grade
            $grade = $this->extractGradeOnly($line2);
            
            if ($subject && $grade && !isset($grades[$subject])) {
                $grades[$subject] = $grade;
            }
        }
        
        return $grades;
    }

    /**
     * Extract from raw text using regex
     */
    private function extractFromRawText($text)
    {
        $grades = [];
        
        // Pattern: Subject name followed by grade with optional text in between
        $pattern = '/(BAHASA MELAYU|BAHASA INGGERIS|PENDIDIKAN ISLAM|SEJARAH|MATHEMATICS|ADDITIONAL MATHEMATICS|GRAFIK KOMUNIKASI TEKNIKAL|PHYSICS|CHEMISTRY)\s*[A-Z\s]*\s*(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)/i';
        
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $subject = trim($match[1]);
                $grade = $this->correctGrade(trim($match[2]));
                
                $standardSubject = $this->matchSubject($subject);
                if ($standardSubject && !isset($grades[$standardSubject])) {
                    $grades[$standardSubject] = $grade;
                }
            }
        }
        
        return $grades;
    }

    /**
     * Extract grade only from a line
     */
    private function extractGradeOnly($line)
    {
        $line = preg_replace('/\([^)]*\)/', '', $line);
        $line = trim($line);
        
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
            '/\b(G)\b/i' => 'G',
            '/\b(As)\b/i' => 'A-',
            '/\b(Ae)\b/i' => 'A-',
            '/\b(AY)\b/i' => 'A+',
        ];
        
        foreach ($patterns as $pattern => $grade) {
            if (preg_match($pattern, $line)) {
                return $this->correctGrade($grade);
            }
        }
        
        return null;
    }

    /**
     * Correct OCR misread grades
     */
    private function correctGrade($grade)
    {
        $grade = strtoupper(trim($grade));
        
        $corrections = [
            'AS' => 'A-',
            'AE' => 'A-',
            'AY' => 'A+',
            'A+' => 'A+',
            'A-' => 'A-',
            'A' => 'A',
            'AT' => 'A+',
            'A®' => 'A+',
            'A?' => 'A+',
            'A*' => 'A+',
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
        ];
        
        if (isset($corrections[$grade])) {
            return $corrections[$grade];
        }
        
        return $grade;
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
                if ($subject === $variation || stripos($subject, $variation) !== false) {
                    return $standard;
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
        $validGrades = array_keys(self::GRADE_POINTS);
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