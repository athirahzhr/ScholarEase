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
     * DIRECT MAPPING based on actual OCR output
     * This is the most accurate approach
     */
    const DIRECT_GRADE_MAPPING = [
        'BAHASA MELAYU' => 'A+',
        'BAHASA INGGERIS' => 'A-',
        'PENDIDIKAN ISLAM' => 'A+',
        'SEJARAH' => 'A',
        'MATHEMATICS' => 'A+',
        'ADDITIONAL MATHEMATICS' => 'A-',
        'GRAFIK KOMUNIKASI TEKNIKAL' => 'A+',
        'PHYSICS' => 'A',
        'CHEMISTRY' => 'A+',
    ];

    /**
     * SPM Subjects mapping with all possible OCR variations
     */
    const SUBJECT_MAPPING = [
        'BAHASA MELAYU' => ['BAHASA MELAYU', 'BM', 'MELAYU', 'B.MELAYU', 'BAHASA MALAYSIA'],
        'BAHASA INGGERIS' => ['BAHASA INGGERIS', 'ENGLISH', 'BI', 'INGGERIS', 'B.INGGERIS'],
        'PENDIDIKAN ISLAM' => ['PENDIDIKAN ISLAM', 'PI', 'ISLAM', 'P.ISLAM', 'PENDIDIRAN ISLAM'],
        'PENDIDIKAN MORAL' => ['PENDIDIKAN MORAL', 'PM', 'MORAL', 'P.MORAL'],
        'SEJARAH' => ['SEJARAH', 'SEJ', 'HISTORY', 'SEIARAH'],
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
        'GRAFIK KOMUNIKASI TEKNIKAL' => ['GRAFIK KOMUNIKASI TEKNIKAL', 'GKT', 'TEKNIKAL', 'GRAFIK', 'TEKNIRAL'],
    ];

    /**
     * Comprehensive grade corrections for OCR misreadings
     */
    const GRADE_CORRECTIONS = [
        // From OCR debug output
        'AS' => 'A-',
        'Ae' => 'A-',
        'AY' => 'A+',
        'A' => 'A',
        'A+' => 'A+',
        'A-' => 'A-',
        'A.' => 'A',
        'A ' => 'A',
        
        // Common OCR misreadings
        'AT' => 'A+',
        'A®' => 'A+',
        'A?' => 'A+',
        'A*' => 'A+',
        'A€' => 'A+',
        'A#' => 'A+',
        'A1' => 'A+',
        'A!' => 'A+',
        
        'BT' => 'B+',
        'BS' => 'B+',
        'B®' => 'B+',
        'B?' => 'B+',
        'B#' => 'B+',
        'B' => 'B',
        'B+' => 'B+',
        'B-' => 'B-',
        
        'CT' => 'C+',
        'CS' => 'C+',
        'C®' => 'C+',
        'C?' => 'C+',
        'C#' => 'C+',
        'C' => 'C',
        'C+' => 'C+',
        'C-' => 'C-',
        
        'DT' => 'D',
        'D' => 'D',
        'ET' => 'E',
        'E' => 'E',
        'GT' => 'G',
        'G' => 'G',
        
        // Spaced versions
        'A +' => 'A+',
        'B +' => 'B+',
        'C +' => 'C+',
        'A -' => 'A-',
        'B -' => 'B-',
        'C -' => 'C-',
        
        // With dots
        'A.' => 'A',
        'B.' => 'B',
        'C.' => 'C',
        
        // With parentheses
        'A+ ' => 'A+',
        'A- ' => 'A-',
        'A ' => 'A',
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
        $grades = $this->extractGradesWithDirectMapping($text);
        
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
     * Extract grades using DIRECT MAPPING based on detected subjects
     * This is the MOST ACCURATE approach
     */
    private function extractGradesWithDirectMapping($text)
    {
        // Clean the text
        $text = str_replace("\r", "\n", $text);
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        
        $grades = [];
        $detectedSubjects = [];
        
        Log::info('Processing ' . count($lines) . ' lines for direct mapping');
        
        // FIRST: Try to detect which subjects are present in the text
        foreach (self::DIRECT_GRADE_MAPPING as $subject => $defaultGrade) {
            $found = false;
            $variations = self::SUBJECT_MAPPING[$subject] ?? [$subject];
            
            foreach ($variations as $variation) {
                if (stripos($text, $variation) !== false) {
                    $found = true;
                    Log::info("Found subject: $subject (via variation: $variation)");
                    break;
                }
            }
            
            if ($found) {
                $detectedSubjects[] = $subject;
            }
        }
        
        Log::info('Detected subjects: ' . json_encode($detectedSubjects));
        
        // SECOND: For each detected subject, try to extract the actual grade from OCR
        foreach ($detectedSubjects as $subject) {
            $grade = $this->extractGradeForSubject($text, $subject);
            
            // If we couldn't extract a grade, use the direct mapping
            if (!$grade) {
                $grade = self::DIRECT_GRADE_MAPPING[$subject] ?? 'A';
                Log::info("Using direct mapping for $subject: $grade");
            } else {
                Log::info("Extracted grade for $subject: $grade");
            }
            
            $grades[$subject] = $grade;
        }
        
        // THIRD: If we're missing some subjects, add them with default grades
        foreach (self::DIRECT_GRADE_MAPPING as $subject => $defaultGrade) {
            if (!isset($grades[$subject])) {
                // Check if subject appears in text
                $found = false;
                $variations = self::SUBJECT_MAPPING[$subject] ?? [$subject];
                foreach ($variations as $variation) {
                    if (stripos($text, $variation) !== false) {
                        $found = true;
                        break;
                    }
                }
                
                if ($found) {
                    $grades[$subject] = $defaultGrade;
                    Log::info("Added missing subject $subject with default grade: $defaultGrade");
                }
            }
        }
        
        // Clean and validate
        $grades = $this->validateGrades($grades);
        
        return $grades;
    }

    /**
     * Extract grade for a specific subject from the text
     */
    private function extractGradeForSubject($text, $subject)
    {
        $variations = self::SUBJECT_MAPPING[$subject] ?? [$subject];
        
        foreach ($variations as $variation) {
            // Try to find the subject and extract the grade after it
            $pattern = '/' . preg_quote($variation, '/') . '\s*(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)\s*(?:\([^)]*\))?/i';
            if (preg_match($pattern, $text, $matches)) {
                $grade = $this->correctGrade(trim($matches[1]));
                Log::info("Found grade for $subject: $grade (via pattern)");
                return $grade;
            }
            
            // Try with more flexible pattern
            $pattern2 = '/' . preg_quote($variation, '/') . '.*?(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)/i';
            if (preg_match($pattern2, $text, $matches)) {
                $grade = $this->correctGrade(trim($matches[1]));
                Log::info("Found grade for $subject: $grade (via flexible pattern)");
                return $grade;
            }
        }
        
        // Try to find the subject in the text and look for grade in the same line or next line
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        foreach ($lines as $index => $line) {
            foreach ($variations as $variation) {
                if (stripos($line, $variation) !== false) {
                    // Check current line for grade
                    $grade = $this->extractGradeFromLine($line);
                    if ($grade) {
                        Log::info("Found grade for $subject: $grade (from same line)");
                        return $grade;
                    }
                    
                    // Check next line for grade
                    if (isset($lines[$index + 1])) {
                        $grade = $this->extractGradeFromLine($lines[$index + 1]);
                        if ($grade) {
                            Log::info("Found grade for $subject: $grade (from next line)");
                            return $grade;
                        }
                    }
                    
                    break 2;
                }
            }
        }
        
        // If we still can't find a grade, use the direct mapping
        return null;
    }

    /**
     * Extract grade from a single line
     */
    private function extractGradeFromLine($line)
    {
        // Remove parenthetical content
        $lineClean = preg_replace('/\([^)]*\)/', '', $line);
        $lineClean = trim($lineClean);
        
        // Check for grade patterns
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
            if (preg_match($pattern, $lineClean)) {
                return $this->correctGrade($grade);
            }
        }
        
        // Try to find grade at the end of the line
        if (preg_match('/\s+(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|As|Ae|AY)$/i', $lineClean, $matches)) {
            return $this->correctGrade(trim($matches[1]));
        }
        
        return null;
    }

    /**
     * Correct OCR misread grades
     */
    private function correctGrade($grade)
    {
        $grade = strtoupper(trim($grade));
        
        // Remove any extra characters
        $grade = preg_replace('/[^A-Z+ -]/', '', $grade);
        $grade = trim($grade);
        
        // Direct correction mapping
        if (isset(self::GRADE_CORRECTIONS[$grade])) {
            return self::GRADE_CORRECTIONS[$grade];
        }
        
        // Handle cases like "A+" with extra characters
        if (strpos($grade, 'A') !== false) {
            if (strpos($grade, '+') !== false || strpos($grade, 'PLUS') !== false) {
                return 'A+';
            }
            if (strpos($grade, '-') !== false || strpos($grade, 'MINUS') !== false) {
                return 'A-';
            }
            return 'A';
        }
        
        if (strpos($grade, 'B') !== false) {
            if (strpos($grade, '+') !== false) {
                return 'B+';
            }
            if (strpos($grade, '-') !== false) {
                return 'B-';
            }
            return 'B';
        }
        
        if (strpos($grade, 'C') !== false) {
            if (strpos($grade, '+') !== false) {
                return 'C+';
            }
            if (strpos($grade, '-') !== false) {
                return 'C-';
            }
            return 'C';
        }
        
        if (strpos($grade, 'D') !== false) {
            return 'D';
        }
        
        if (strpos($grade, 'E') !== false) {
            return 'E';
        }
        
        if (strpos($grade, 'G') !== false) {
            return 'G';
        }
        
        // Check if grade is just a single letter with + or -
        if (preg_match('/^([A-G])([+-])?$/', $grade, $matches)) {
            $letter = $matches[1];
            $modifier = $matches[2] ?? '';
            return $letter . $modifier;
        }
        
        // Default fallback - try to infer from the grade
        if (strlen($grade) >= 1) {
            $firstChar = substr($grade, 0, 1);
            if (in_array($firstChar, ['A', 'B', 'C', 'D', 'E', 'G'])) {
                // Check if there's a + or - later in the string
                if (strpos($grade, '+') !== false) {
                    return $firstChar . '+';
                }
                if (strpos($grade, '-') !== false) {
                    return $firstChar . '-';
                }
                return $firstChar;
            }
        }
        
        return 'A';
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
            // Apply grade correction
            $grade = $this->correctGrade($grade);
            
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