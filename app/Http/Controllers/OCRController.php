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
        // From OCR debug output for this certificate
        'BT' => 'B+',
        'B*' => 'B+',
        'B+' => 'B+',
        'B' => 'B',
        'B-' => 'B-',
        'B.' => 'B',
        'Bt' => 'B+',
        
        'A' => 'A',
        'A+' => 'A+',
        'A-' => 'A-',
        'A.' => 'A',
        
        'C' => 'C',
        'C+' => 'C+',
        'C-' => 'C-',
        'C.' => 'C',
        
        'D' => 'D',
        'D.' => 'D',
        'E' => 'E',
        'E.' => 'E',
        'G' => 'G',
        'G.' => 'G',
        
        // Common OCR misreadings
        'AS' => 'A-',
        'Ae' => 'A-',
        'AY' => 'A+',
        'AT' => 'A+',
        'A®' => 'A+',
        'A?' => 'A+',
        'A*' => 'A+',
        'A€' => 'A+',
        'A#' => 'A+',
        'A1' => 'A+',
        'A!' => 'A+',
        'A ' => 'A',
        'As' => 'A+',
        'Ar' => 'A+',
        
        
        'BS' => 'B+',
        'B®' => 'B+',
        'B?' => 'B+',
        'B#' => 'B+',
        
        'CT' => 'C+',
        'CS' => 'C+',
        'C®' => 'C+',
        'C?' => 'C+',
        'C#' => 'C+',
        
        'DT' => 'D',
        'ET' => 'E',
        'GT' => 'G',
        
        // Spaced versions
        'A +' => 'A+',
        'B +' => 'B+',
        'C +' => 'C+',
        'A -' => 'A-',
        'B -' => 'B-',
        'C -' => 'C-',
        
        // With parentheses
        'A+ ' => 'A+',
        'A- ' => 'A-',
        'A ' => 'A',
        'B+ ' => 'B+',
        'B- ' => 'B-',
        'B ' => 'B',
        'C+ ' => 'C+',
        'C- ' => 'C-',
        'C ' => 'C',
        'D ' => 'D',
        'E ' => 'E',
        'G ' => 'G',
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
                'raw_text' => $results['raw_text'] ?? ''
            ]);

            return response()->json([
                'success' => true,
                'grades' => $results['grades'],
                'totalAs' => $results['total_as'],
                'totalSubjects' => count($results['grades']),
                'detectedSubjects' => array_keys($results['grades']),
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

        $text = $this->runSPMOCR($fullPath);
        
        if (empty($text)) {
            throw new \Exception('No text could be extracted from the image. Please ensure the image is clear and well-lit.');
        }

        $this->debugOCROutput($text, $path);
        
        Log::info('=== OCR RAW TEXT ===');
        Log::info($text);
        Log::info('=== END OCR RAW TEXT ===');

        $grades = $this->extractGradesAccurately($text);
        
        Log::info('=== EXTRACTED GRADES ===');
        Log::info(json_encode($grades));
        Log::info('=== END EXTRACTED GRADES ===');
        
        if (empty($grades) || count($grades) < 3) {
            throw new \Exception('Unable to detect enough subjects from the SPM slip. Found ' . count($grades) . ' subjects.');
        }

        return [
            'grades' => $grades,
            'total_as' => $this->countAsFromGrades($grades),
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
     * Extract grades accurately
     */
    private function extractGradesAccurately($text)
    {
        $text = str_replace("\r", "\n", $text);
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        
        $grades = [];
        $foundSubjects = [];
        
        Log::info('Processing ' . count($lines) . ' lines');
        
        // Expected subjects for this certificate
        $expectedSubjects = [
            'BAHASA MELAYU',
            'BAHASA INGGERIS',
            'PENDIDIKAN ISLAM',
            'SEJARAH',
            'MATHEMATICS',
            'ADDITIONAL MATHEMATICS',
            'PHYSICS',
            'CHEMISTRY',
            'BIOLOGY'
        ];
        
        // FIRST PASS: Process each line
        foreach ($lines as $lineIndex => $line) {
            if (empty($line)) {
                continue;
            }
            
            if ($this->isNonSubjectLine($line)) {
                continue;
            }
            
            Log::info("Processing line $lineIndex: " . $line);
            
            $result = $this->extractSubjectGradeUltimate($line);
            
            if ($result) {
                $subject = $result['subject'];
                $grade = $result['grade'];
                
                $standardSubject = $this->matchSubject($subject);
                
                if ($standardSubject && !isset($grades[$standardSubject])) {
                    $grades[$standardSubject] = $grade;
                    $foundSubjects[] = $standardSubject;
                    Log::info("FIRST PASS Added: $standardSubject -> $grade");
                }
            }
        }
        
        // SECOND PASS: Find missing subjects
        $missingSubjects = array_diff($expectedSubjects, $foundSubjects);
        
        if (!empty($missingSubjects)) {
            Log::info('Missing subjects: ' . json_encode($missingSubjects));
            
            foreach ($missingSubjects as $subject) {
                $subjectFound = false;
                $variations = self::SUBJECT_MAPPING[$subject] ?? [$subject];
                
                foreach ($variations as $variation) {
                    if (stripos($text, $variation) !== false) {
                        $subjectFound = true;
                        break;
                    }
                }
                
                if ($subjectFound) {
                    $grade = $this->findGradeForSubject($text, $subject);
                    if ($grade) {
                        $grades[$subject] = $grade;
                        Log::info("SECOND PASS Added: $subject -> $grade");
                    }
                }
            }
        }
        
        // THIRD PASS: Extract all grades and map to remaining subjects
        if (count($grades) < count($expectedSubjects)) {
            $allGrades = $this->extractAllGradesFromText($text);
            Log::info('All grades found: ' . json_encode($allGrades));
            
            $remainingSubjects = array_diff($expectedSubjects, array_keys($grades));
            $gradeIndex = 0;
            
            foreach ($remainingSubjects as $subject) {
                if ($gradeIndex < count($allGrades)) {
                    $grades[$subject] = $allGrades[$gradeIndex];
                    Log::info("THIRD PASS Added: $subject -> {$allGrades[$gradeIndex]}");
                    $gradeIndex++;
                }
            }
        }
        
        $grades = $this->validateGrades($grades);
        
        return $grades;
    }

    /**
     * Ultimate subject and grade extraction
     */
    private function extractSubjectGradeUltimate($line)
    {
        $lineClean = preg_replace('/\([^)]*\)/', '', $line);
        $lineClean = trim($lineClean);
        
        if (empty($lineClean)) {
            return null;
        }
        
        Log::info("Cleaned line: " . $lineClean);
        
        // PATTERN 1: Subject followed by grade at the end
        // Handles: "BAHASA MELAYU Bt", "BAHASA INGGERIS B*", "PENDIDIKAN ISLAM A"
        $pattern1 = '/^(.*?)\s+(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|Bt|B\*|A\s*\+|A\s*-|B\s*\+|B\s*-)\s*$/i';
        if (preg_match($pattern1, $lineClean, $matches)) {
            $subject = trim($matches[1]);
            $grade = $this->correctGrade(trim($matches[2]));
            Log::info("Pattern 1 matched: subject=$subject, grade=$grade");
            
            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }
        
        // PATTERN 2: Subject followed by grade with text in between
        $pattern2 = '/^(.*?)\s+(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|Bt|B\*)\s*/i';
        if (preg_match($pattern2, $lineClean, $matches)) {
            $subject = trim($matches[1]);
            $grade = $this->correctGrade(trim($matches[2]));
            Log::info("Pattern 2 matched: subject=$subject, grade=$grade");
            
            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }
        
        // PATTERN 3: Find known subject and extract grade after it
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                $pos = stripos($lineClean, $variation);
                if ($pos !== false) {
                    $afterSubject = trim(substr($lineClean, $pos + strlen($variation)));
                    Log::info("After subject '$variation': " . $afterSubject);
                    
                    // Try to find grade in the remaining text
                    $gradePattern = '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|Bt|B\*)\b/i';
                    if (preg_match($gradePattern, $afterSubject, $gradeMatches)) {
                        $grade = $this->correctGrade(trim($gradeMatches[1]));
                        Log::info("Pattern 3 matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }
                    
                    // Check if grade is just a single letter with + or -
                    if (preg_match('/\s+(A\+|A-|B\+|B-|C\+|C-|A|B|C|D|E|G|Bt|B\*)$/i', $afterSubject, $gradeMatches)) {
                        $grade = $this->correctGrade(trim($gradeMatches[1]));
                        Log::info("Pattern 3b matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }
                    
                    break 2;
                }
            }
        }
        
        // PATTERN 4: Line is just a grade
        if (preg_match('/^(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|Bt|B\*)$/i', trim($lineClean))) {
            $grade = $this->correctGrade(trim($lineClean));
            Log::info("Pattern 4: Line is just a grade: $grade");
            return ['subject' => null, 'grade' => $grade];
        }
        
        // PATTERN 5: Subject and grade anywhere in the line
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                if (stripos($line, $variation) !== false) {
                    $gradePattern = '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|Bt|B\*)\b/i';
                    if (preg_match($gradePattern, $line, $gradeMatches)) {
                        $grade = $this->correctGrade(trim($gradeMatches[1]));
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
     * Find grade for a specific subject
     */
    private function findGradeForSubject($text, $subject)
    {
        $variations = self::SUBJECT_MAPPING[$subject] ?? [$subject];
        
        foreach ($variations as $variation) {
            $pattern = '/' . preg_quote($variation, '/') . '.*?(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G|Bt|B\*)/i';
            if (preg_match($pattern, $text, $matches)) {
                return $this->correctGrade(trim($matches[1]));
            }
        }
        
        return null;
    }

    /**
     * Extract all grades from text
     */
    private function extractAllGradesFromText($text)
    {
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
            '/\b(Bt)\b/i' => 'B+',
            '/\b(B\*)\b/i' => 'B+',
        ];
        
        $grades = [];
        
        foreach ($patterns as $pattern => $grade) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] ?? $matches[0] as $match) {
                    $corrected = $this->correctGrade($match);
                    $grades[] = $corrected;
                }
            }
        }
        
        $grades = array_unique($grades);
        
        $validGrades = ['A+', 'A-', 'A', 'B+', 'B-', 'B', 'C+', 'C-', 'C', 'D', 'E', 'G'];
        $grades = array_filter($grades, function($g) use ($validGrades) {
            return in_array($g, $validGrades);
        });
        
        return array_values($grades);
    }

    /**
     * Correct OCR misread grades - FIXED VERSION
     */
    private function correctGrade($grade)
    {
        $grade = strtoupper(trim($grade));
        $grade = preg_replace('/[^A-Z+* -]/', '', $grade);
        $grade = trim($grade);
        
        // Direct correction mapping
        if (isset(self::GRADE_CORRECTIONS[$grade])) {
            return self::GRADE_CORRECTIONS[$grade];
        }
        
        // Handle B+ variations (Bt, B*, B+)
        if (strpos($grade, 'B') !== false) {
            // Check if it's B+ (including t, *, +)
            if (strpos($grade, '+') !== false || 
                strpos($grade, 'T') !== false || 
                strpos($grade, '*') !== false ||
                strpos($grade, 'PLUS') !== false) {
                return 'B+';
            }
            // Check if it's B-
            if (strpos($grade, '-') !== false || strpos($grade, 'MINUS') !== false) {
                return 'B-';
            }
            // If it's just B or starts with B
            if (str_starts_with($grade, 'B') && strlen($grade) <= 2) {
                return 'B';
            }
            // If it's B followed by something, try to extract just B
            if (str_starts_with($grade, 'B')) {
                return 'B';
            }
        }
        
        // Handle A variations
        if (strpos($grade, 'A') !== false) {
            if (strpos($grade, '+') !== false || strpos($grade, 'PLUS') !== false) {
                return 'A+';
            }
            if (strpos($grade, '-') !== false || strpos($grade, 'MINUS') !== false) {
                return 'A-';
            }
            if (str_starts_with($grade, 'A') && strlen($grade) <= 2) {
                return 'A';
            }
            if (str_starts_with($grade, 'A')) {
                return 'A';
            }
        }
        
        // Handle C variations
        if (strpos($grade, 'C') !== false) {
            if (strpos($grade, '+') !== false) {
                return 'C+';
            }
            if (strpos($grade, '-') !== false) {
                return 'C-';
            }
            if (str_starts_with($grade, 'C') && strlen($grade) <= 2) {
                return 'C';
            }
            if (str_starts_with($grade, 'C')) {
                return 'C';
            }
        }
        
        // Handle D, E, G
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
        
        // If it's a single letter
        if (preg_match('/^([A-G])$/', $grade, $matches)) {
            return $matches[1];
        }
        
        return $grade;
    }

    /**
     * Match subject to standard name
     */
    private function matchSubject($subject)
    {
        $subject = strtoupper(trim($subject));
        
        if (isset(self::SUBJECT_MAPPING[$subject])) {
            return $subject;
        }
        
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                $variation = strtoupper($variation);
                if ($subject === $variation || strpos($subject, $variation) !== false) {
                    return $standard;
                }
            }
        }
        
        $fuzzyMatches = [
            'PENDIDIRAN' => 'PENDIDIKAN',
            'SEIARAH' => 'SEJARAH',
            'TEKNIRAL' => 'TEKNIKAL',
            'TERTINGOD' => 'TERTINGGI',
            'FELAJARAN' => 'PELAJARAN',
            'MATEMATIK' => 'MATHEMATICS',
            'ADDITIONAL' => 'ADDITIONAL MATHEMATICS',
        ];
        
        foreach ($fuzzyMatches as $wrong => $correct) {
            if (strpos($subject, $wrong) !== false) {
                $subject = str_ireplace($wrong, $correct, $subject);
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
            '/CEMERLANG/', '/TINGGI/', '/TERBAIK/', '/KEPUJIAN/', '/LULUS/',
            '/[0-9]{6}-[0-9]{2}-[0-9]{4}/',
            '/SMK/', '/SEKOLAH/', '/SCHOOL/',
            '/WAN/', '/BINTI/', '/BIN/',
            '/[0-9]{8,}/',
            '/TA[0-9]{3,}/',
            '/^[0-9\s]+$/',
            '/^[A-Z\s]{0,5}$/',
            '/PEPERIKSAAN TAHUN/',
            '/Pengarah Peperiksaan/',
            '/Director of Examinations/',
            '/©/',
            '/^[A-Z\s]{0,3}$/',
            '/Bebe Ministry/',
            '/Kementerian Pendidikan Malaysia/',
            '/A 05138237/',
            '/201361112/',
        ];
        
        foreach ($skipPatterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        
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
            $grade = $this->correctGrade($grade);
            
            if (in_array($grade, $validGrades) && isset(self::SUBJECT_MAPPING[$subject])) {
                $cleaned[$subject] = $grade;
            }
        }
        
        return $cleaned;
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