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
     * Allowed grade values (no B- or C- in SPM)
     */
    const GRADE_POINTS = [
        'A+' => 12,
        'A' => 11,
        'A-' => 10,
        'B+' => 9,
        'B' => 8,
        'C+' => 6,
        'C' => 5,
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
        'PENDIDIKAN SENI VISUAL' => ['PENDIDIKAN SENI VISUAL', 'SENI', 'ART', 'PSV', 'PENDIDIKAN SENI'],
        'REKA CIPTA' => ['REKA CIPTA', 'RBT'],
        'ASAS SAINS KOMPUTER' => ['ASAS SAINS KOMPUTER', 'ASK', 'COMPUTER SCIENCE'],
        'BAHASA ARAB' => ['BAHASA ARAB', 'ARAB', 'B.ARAB'],
        'BAHASA CINA' => ['BAHASA CINA', 'CINA', 'CHINESE', 'B.CINA'],
        'BAHASA TAMIL' => ['BAHASA TAMIL', 'TAMIL', 'B.TAMIL'],
        'GRAFIK KOMUNIKASI TEKNIKAL' => ['GRAFIK KOMUNIKASI TEKNIKAL', 'GKT', 'TEKNIKAL', 'GRAFIK', 'TEKNIRAL'],
        'PERNIAGAAN' => ['PERNIAGAAN', 'PERNIAGA'],
    ];

    /**
     * Comprehensive grade corrections for OCR misreadings (no B- or C-)
     */
    const GRADE_CORRECTIONS = [
        'BT' => 'B+',
        'B*' => 'B+',
        'B+' => 'B+',
        'B' => 'B',
        'B.' => 'B',
        'Bt' => 'B+',
        'B"' => 'B+',
        "B'" => 'B+',

        'A' => 'A',
        'A+' => 'A+',
        'A-' => 'A-',
        'A.' => 'A',
        'A"' => 'A-',
        "A'" => 'A-',

        'C' => 'C',
        'C+' => 'C+',
        'C.' => 'C',

        'D' => 'D',
        'D.' => 'D',
        'E' => 'E',
        'E.' => 'E',
        'G' => 'G',
        'G.' => 'G',

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

        'A +' => 'A+',
        'B +' => 'B+',
        'C +' => 'C+',
        'A -' => 'A-',
    ];

    /**
     * Generic field-label patterns found on every SPM certificate.
     * These describe STRUCTURE (labels, ID formats), never a specific
     * student's name/IC/angka giliran — so they generalize to any slip.
     */
    const SKIP_LINE_PATTERNS = [
        // Header / issuing body
        '/SIJIL/i', '/PELAJARAN/i', '/LEMBAGA/i', '/PEPERIKSAAN/i',
        '/KEMENTERIAN/i', '/PENDIDIKAN MALAYSIA/i', '/MINISTRY/i', '/EDUCATION/i',
        '/PENGARAH/i', '/DIRECTOR/i',

        // Student info field labels (label text, not the value)
        '/^\s*NAMA\s*:/i',
        '/^\s*NO\.?\s*PENGENALAN/i',
        '/^\s*ANGKA\s*GILIRAN/i',
        '/^\s*SEKOLAH\s*:/i',
        '/^\s*JUMLAH\s*MATA\s*PELAJARAN/i',
        '/CALON/i', '/CANDIDATE/i',

        // Generic ID number FORMATS (not any specific number)
        '/\b\d{6}-\d{2}-\d{4}\b/',      // IC number format e.g. 061114-01-1090
        '/\b[A-Z]{1,3}\d{3,}[A-Z]?\d*\b/', // Angka giliran style codes e.g. JF026A119

        // Table headers
        '/^\s*KOD\b/i', '/NAMA\s*MATA\s*PELAJARAN/i', '/^\s*GRED\b/i',

        // Footer remarks (kept narrow — only the label prefix, not full line,
        // so we don't need to guess every possible CEFR band/remark word)
        '/^\s*LAYAK\s*MENDAPAT\s*SIJIL/i',
        '/^\s*UJIAN\s*LISAN/i',
        '/^\s*TAHAP\s*KESELURUHAN/i',

        // Pure numeric or too-short junk lines
        '/^[0-9\s]+$/',
        '/^[A-Z\s]{0,3}$/i',
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

        // NOTE: Image preprocessing (watermark suppression via Imagick) is
        // disabled — Imagick isn't installed. OCR runs on the original file.
        // This only affects the SAINS/watermark-overlap edge case; the
        // skip-pattern and regex fixes below still apply and fix the two
        // bigger issues (B+ grades being dropped, subject rows being skipped).
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
     * Extract grades accurately.
     * Only saves subjects that are actually detected from OCR.
     */
    private function extractGradesAccurately($text)
    {
        $text = str_replace("\r", "\n", $text);
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        $grades = [];

        Log::info('Processing ' . count($lines) . ' lines for subject detection');

        // FIRST PASS: line-by-line, most accurate
        foreach ($lines as $lineIndex => $line) {
            if (empty($line) || $this->isNonSubjectLine($line)) {
                continue;
            }

            Log::info("Processing line $lineIndex: " . $line);

            $result = $this->extractSubjectGradeUltimate($line);

            if ($result) {
                $subject = $result['subject'];
                $grade = $result['grade'];

                $standardSubject = $this->matchSubject($subject);

                if ($standardSubject && isset(self::SUBJECT_MAPPING[$standardSubject]) && !isset($grades[$standardSubject])) {
                    $grades[$standardSubject] = $grade;
                    Log::info("DETECTED: $standardSubject -> $grade");
                }
            }
        }

        // SECOND PASS: catch subjects the line pass may have missed
        foreach (array_keys(self::SUBJECT_MAPPING) as $subject) {
            if (isset($grades[$subject])) {
                continue;
            }

            $variations = self::SUBJECT_MAPPING[$subject] ?? [$subject];
            $found = false;

            foreach ($variations as $variation) {
                if (stripos($text, $variation) !== false) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $grade = $this->findGradeForSubject($text, $subject);
                if ($grade) {
                    $grades[$subject] = $grade;
                    Log::info("SECOND PASS DETECTED: $subject -> $grade");
                }
            }
        }

        Log::info('=== DETECTED SUBJECTS (' . count($grades) . ') ===');
        Log::info(json_encode($grades));
        Log::info('=== END DETECTED SUBJECTS ===');

        $grades = $this->validateGrades($grades);

        return $grades;
    }

    /**
     * Find grade for a specific subject in text.
     *
     * FIX: the old pattern ended in a plain \b, which fails right after
     * "+" (since + and the following space/newline are both non-word
     * characters, there's no boundary there) — causing "B+" to silently
     * match as just "B". Using a negative lookahead (?!\w) instead fixes
     * this: it only requires that the match isn't immediately followed by
     * another word character, so "+" and "-" survive.
     */
    private function findGradeForSubject($text, $subject)
    {
        $variations = self::SUBJECT_MAPPING[$subject] ?? [$subject];

        foreach ($variations as $variation) {
            $pos = stripos($text, $variation);
            if ($pos !== false) {
                $afterSubject = substr($text, $pos + strlen($variation), 50);

                $gradePattern = '/\b(A\+|A-|A|B\+|B|C\+|C|D|E|G|Bt|B\*|B["\']|A["\'])(?!\w)/i';
                if (preg_match($gradePattern, $afterSubject, $matches)) {
                    return $this->correctGrade(trim($matches[1]));
                }

                if (preg_match('/\b([A-G])(?!\w)/', $afterSubject, $matches)) {
                    return $this->correctGrade(trim($matches[1]));
                }
            }
        }

        return null;
    }

    /**
     * Ultimate subject and grade extraction.
     */
    private function extractSubjectGradeUltimate($line)
    {
        $lineClean = preg_replace('/\([^)]*\)/', '', $line);
        $lineClean = trim($lineClean);

        if (empty($lineClean)) {
            return null;
        }

        Log::info("Cleaned line: " . $lineClean);

        // PATTERN 1: Subject followed by grade at the end of the line
        $pattern1 = '/^(.*?)\s+([A-G][\+\-]?|Bt|B\*|A\s*\+|A\s*-|B\s*\+|B["\']|A["\'])\s*$/i';
        if (preg_match($pattern1, $lineClean, $matches)) {
            $subject = trim($matches[1]);
            $grade = $this->correctGrade(trim($matches[2]));
            Log::info("Pattern 1 matched: subject=$subject, grade=$grade");

            if (!empty($subject) && !empty($grade)) {
                return ['subject' => $subject, 'grade' => $grade];
            }
        }

        // PATTERN 2: Known subject with grade after it
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                $pos = stripos($lineClean, $variation);
                if ($pos !== false) {
                    $afterSubject = trim(substr($lineClean, $pos + strlen($variation)));
                    Log::info("After subject '$variation': " . $afterSubject);

                    // FIX: (?!\w) instead of trailing \b — see note above.
                    $gradePattern = '/^([A-G][\+\-]?|Bt|B\*|B["\']|A["\'])(?!\w)/i';
                    if (preg_match($gradePattern, $afterSubject, $gradeMatches)) {
                        $grade = $this->correctGrade(trim($gradeMatches[1]));
                        Log::info("Pattern 2 matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }

                    $gradePattern2 = '/\b([A-G][\+\-]?|Bt|B\*)(?!\w)/i';
                    if (preg_match($gradePattern2, $afterSubject, $gradeMatches)) {
                        $grade = $this->correctGrade(trim($gradeMatches[1]));
                        Log::info("Pattern 2b matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }

                    break 2;
                }
            }
        }

        // PATTERN 3: Line is just a grade, no subject — skip
        if (preg_match('/^([A-G][\+\-]?|Bt|B\*)$/i', trim($lineClean))) {
            Log::info("Pattern 3: Line is just a grade, skipping");
            return null;
        }

        // PATTERN 4: Subject anywhere in line with a grade nearby
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                if (stripos($line, $variation) !== false) {
                    $gradePattern = '/\b([A-G][\+\-]?|Bt|B\*|B["\']|A["\'])(?!\w)/i';
                    if (preg_match($gradePattern, $line, $gradeMatches)) {
                        $grade = $this->correctGrade(trim($gradeMatches[1]));
                        Log::info("Pattern 4 matched: subject=$standard, grade=$grade");
                        return ['subject' => $standard, 'grade' => $grade];
                    }
                    break 2;
                }
            }
        }

        return null;
    }

    /**
     * Correct OCR misread grades (no B- or C-)
     */
    private function correctGrade($grade)
    {
        $grade = strtoupper(trim($grade));
        $grade = preg_replace('/[^A-Z+* -]/', '', $grade);
        $grade = trim($grade);

        if (isset(self::GRADE_CORRECTIONS[$grade])) {
            return self::GRADE_CORRECTIONS[$grade];
        }

        if (strpos($grade, 'B') !== false) {
            if (strpos($grade, '+') !== false ||
                strpos($grade, 'T') !== false ||
                strpos($grade, '*') !== false ||
                strpos($grade, 'PLUS') !== false ||
                preg_match('/B\s*\+/', $grade) ||
                preg_match('/B["\']/', $grade)) {
                return 'B+';
            }
            if (str_starts_with($grade, 'B') && strlen($grade) <= 2) {
                return 'B';
            }
            if (str_starts_with($grade, 'B')) {
                return 'B';
            }
        }

        if (strpos($grade, 'A') !== false) {
            if (strpos($grade, '-') !== false ||
                strpos($grade, 'MINUS') !== false ||
                preg_match('/A["\']/', $grade) ||
                preg_match('/A\s*-/', $grade)) {
                return 'A-';
            }
            if (strpos($grade, '+') !== false ||
                strpos($grade, 'PLUS') !== false) {
                return 'A+';
            }
            if (str_starts_with($grade, 'A') && strlen($grade) <= 2) {
                return 'A';
            }
            if (str_starts_with($grade, 'A')) {
                return 'A';
            }
        }

        if (strpos($grade, 'C') !== false) {
            if (strpos($grade, '+') !== false) {
                return 'C+';
            }
            if (str_starts_with($grade, 'C') && strlen($grade) <= 2) {
                return 'C';
            }
            if (str_starts_with($grade, 'C')) {
                return 'C';
            }
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

        if (preg_match('/^([A-G])([+-])?$/', $grade, $matches)) {
            $letter = $matches[1];
            $modifier = $matches[2] ?? '';
            if ($modifier === '-' && $letter !== 'A') {
                return $letter;
            }
            return $letter . $modifier;
        }

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

        // Generic OCR-misread reassembly: collapse spaces and retry.
        // This replaces one-off hardcoded cases (e.g. "BAHASAMELAYU",
        // "PENDIDIKANISLAM") with a general rule: if the subject with all
        // spaces removed matches a known variation with its spaces
        // removed, treat it as that subject.
        $subjectNoSpace = str_replace(' ', '', $subject);
        foreach (self::SUBJECT_MAPPING as $standard => $variations) {
            foreach ($variations as $variation) {
                $variationNoSpace = str_replace(' ', '', strtoupper($variation));
                if ($subjectNoSpace === $variationNoSpace) {
                    return $standard;
                }
            }
        }

        // Known non-subject table-header fragments that sometimes survive
        // into this function; skip rather than mismatch.
        $ignoreFragments = ['NAMAMATA', 'GRED', 'KOD'];
        foreach ($ignoreFragments as $fragment) {
            if (stripos($subject, $fragment) !== false) {
                return null;
            }
        }

        return null;
    }

    /**
     * Check if a line should be skipped.
     * Uses only GENERIC structural patterns (field labels, ID formats,
     * table headers) — no specific student's name/IC/angka giliran.
     */
    private function isNonSubjectLine($line)
    {
        foreach (self::SKIP_LINE_PATTERNS as $pattern) {
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
     * Validate and clean grades (no B- or C-)
     */
    private function validateGrades($grades)
    {
        $validGrades = ['A+', 'A-', 'A', 'B+', 'B', 'C+', 'C', 'D', 'E', 'G'];
        $cleaned = [];

        foreach ($grades as $subject => $grade) {
            if (!isset(self::SUBJECT_MAPPING[$subject])) {
                Log::warning("Skipping invalid subject: $subject");
                continue;
            }

            $grade = strtoupper(trim($grade));
            $grade = $this->correctGrade($grade);

            if (in_array($grade, $validGrades)) {
                $cleaned[$subject] = $grade;
            } else {
                Log::warning("Invalid grade for $subject: $grade");
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
            'grades.*' => 'required|in:A+,A,A-,B+,B,C+,C,D,E,G'
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
            'grade' => 'required|in:A+,A,A-,B+,B,C+,C,D,E,G'
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