<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRController extends Controller
{
    private const SUBJECTS = [
        'BAHASA MELAYU',
        'BAHASA INGGERIS',
        'PENDIDIKAN ISLAM',
        'PENDIDIKAN MORAL',
        'SEJARAH',
        'MATHEMATICS',
        'MATEMATIK',
        'ADDITIONAL MATHEMATICS',
        'MATEMATIK TAMBAHAN',
        'PHYSICS',
        'FIZIK',
        'CHEMISTRY',
        'KIMIA',
        'BIOLOGY',
        'BIOLOGI',
        'SAINS',
        'GRAFIK KOMUNIKASI TEKNIKAL',
        'BAHASA ARAB',
        'PRINSIP PERAKAUNAN',
        'EKONOMI',
        'PERDAGANGAN',
        'GEOGRAFI',
        'SAINS KOMPUTER',
        'TEKNOLOGI MAKLUMAT DAN KOMUNIKASI',
        'PENDIDIKAN SENI VISUAL',
        'REKA CIPTA',
    ];

    // =========================================================================
    // PUBLIC: Upload
    // =========================================================================

    public function uploadSPM(Request $request)
    {
        $request->validate([
            'spm_file' => 'required|file|mimes:jpg,jpeg,png|max:5120'
        ]);

        try {
            $user     = Auth::user();
            $file     = $request->file('spm_file');
            $filename = 'spm_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('spm_documents', $filename, 'public');

            $results = $this->processRealOCR($path);

            Session::put('ocr_temp_data', [
                'file_path'         => $path,
                'raw_grades'        => $results['grades'],
                'grades'            => $results['grades'],
                'total_as'          => $results['total_as'],
                'detected_subjects' => array_keys($results['grades']),
                'confidence'        => $results['confidence'],
                'timestamp'         => now(),
            ]);

            return response()->json([
                'success'          => true,
                'grades'           => $results['grades'],
                'totalAs'          => $results['total_as'],
                'detectedSubjects' => array_keys($results['grades']),
                'confidence'       => $results['confidence'],
                'message'          => 'SPM results extracted successfully!',
                'allowEdit'        => true,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // CORE PIPELINE
    // =========================================================================

    private function processRealOCR(string $path): array
    {
        $fullPath = storage_path('app/public/' . $path);
        $debug    = [];

        // --- Preprocess: two strategies ---
        $cleanPath    = $this->preprocessClean($fullPath);
        $denoisedPath = $this->preprocessDenoised($fullPath);

        // --- Run Tesseract safely (fallback if msa lang not installed) ---
        $textClean    = $this->runTesseractSafe($cleanPath);
        $textDenoised = $this->runTesseractSafe($denoisedPath);

        // --- Save debug output ---
        $debug['clean_text']    = $textClean;
        $debug['denoised_text'] = $textDenoised;

        // --- Parse both ---
        $resultClean    = $this->parseSPMGradesFromText($textClean);
        $resultDenoised = $this->parseSPMGradesFromText($textDenoised);

        $debug['clean_grades']    = $resultClean['grades'];
        $debug['denoised_grades'] = $resultDenoised['grades'];

        // --- Pick the better result ---
        $result = count($resultClean['grades']) >= count($resultDenoised['grades'])
            ? $resultClean
            : $resultDenoised;

        $debug['chosen'] = count($resultClean['grades']) >= count($resultDenoised['grades'])
            ? 'clean'
            : 'denoised';

        // --- Write full debug log ---
        file_put_contents(
            storage_path('app/ocr_debug.txt'),
            json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // --- Cleanup temp files ---
        @unlink($cleanPath);
        @unlink($denoisedPath);

        // --- Validate ---
        if (!$this->isValidSPMResult($textClean . ' ' . $textDenoised)) {
            throw new \Exception('Uploaded file does not appear to be a valid SPM result slip. Please upload the correct document.');
        }

        if (count($result['grades']) < 3) {
            throw new \Exception(
                'Only ' . count($result['grades']) . ' subject(s) detected. ' .
                'Please upload a clearer, well-lit, straight photo of your SPM result slip.'
            );
        }

        return $result;
    }

    // =========================================================================
    // TESSERACT — safe runner with lang fallback
    // =========================================================================

    private function runTesseractSafe(string $path): string
    {
        // Try English + Malay first
        try {
            return (new TesseractOCR($path))
                ->executable('/usr/bin/tesseract')
                ->lang('eng+msa')
                ->psm(6)   // Uniform block of text
                ->oem(1)   // LSTM only
                ->run();
        } catch (\Throwable $e) {
            // msa not installed — fall back to English only
        }

        // Fallback: English only
        try {
            return (new TesseractOCR($path))
                ->executable('/usr/bin/tesseract')
                ->lang('eng')
                ->psm(6)
                ->oem(1)
                ->run();
        } catch (\Throwable $e) {
            // oem(1) not supported — try default oem
        }

        // Last resort: no flags
        return (new TesseractOCR($path))
            ->executable('/usr/bin/tesseract')
            ->lang('eng')
            ->run();
    }

    // =========================================================================
    // PREPROCESSING — Strategy 1: Clean/plain slip
    // Best for: plain white background result slips
    // =========================================================================

    private function preprocessClean(string $fullPath): string
    {
        $image  = $this->loadImage($fullPath);
        $w      = imagesx($image);
        $h      = imagesy($image);

        // Upscale 3× — critical for Tesseract accuracy
        $big = imagecreatetruecolor($w * 3, $h * 3);
        imagecopyresampled($big, $image, 0, 0, 0, 0, $w * 3, $h * 3, $w, $h);
        imagedestroy($image);

        imagefilter($big, IMG_FILTER_GRAYSCALE);
        imagefilter($big, IMG_FILTER_CONTRAST, -50);
        imagefilter($big, IMG_FILTER_BRIGHTNESS, 5);

        $out = storage_path('app/public/tmp_clean_' . uniqid() . '.png');
        imagepng($big, $out);
        imagedestroy($big);

        return $out;
    }

    // =========================================================================
    // PREPROCESSING — Strategy 2: Denoised for patterned backgrounds
    // Best for: official certificate with guilloché background pattern
    // Uses adaptive thresholding: each pixel compared to its local average
    // so the background pattern (mid-grey) becomes white, text stays black
    // =========================================================================

    private function preprocessDenoised(string $fullPath): string
    {
        $image = $this->loadImage($fullPath);
        $w     = imagesx($image);
        $h     = imagesy($image);

        // Upscale 3×
        $big = imagecreatetruecolor($w * 3, $h * 3);
        imagecopyresampled($big, $image, 0, 0, 0, 0, $w * 3, $h * 3, $w, $h);
        imagedestroy($image);

        // Greyscale
        imagefilter($big, IMG_FILTER_GRAYSCALE);

        $bw    = imagesx($big);
        $bh    = imagesy($big);
        $out   = imagecreatetruecolor($bw, $bh);
        $white = imagecolorallocate($out, 255, 255, 255);
        $black = imagecolorallocate($out, 0, 0, 0);
        imagefill($out, 0, 0, $white);

        // Adaptive threshold — block size 31, constant 10
        $half = 15;
        $C    = 10;

        for ($y = 0; $y < $bh; $y++) {
            for ($x = 0; $x < $bw; $x++) {

                $sum   = 0;
                $count = 0;

                for ($dy = -$half; $dy <= $half; $dy++) {
                    for ($dx = -$half; $dx <= $half; $dx++) {
                        $nx = max(0, min($bw - 1, $x + $dx));
                        $ny = max(0, min($bh - 1, $y + $dy));
                        $sum += (imagecolorat($big, $nx, $ny) >> 16) & 0xFF;
                        $count++;
                    }
                }

                $mean  = $sum / $count;
                $pixel = (imagecolorat($big, $x, $y) >> 16) & 0xFF;

                imagesetpixel($out, $x, $y, $pixel < ($mean - $C) ? $black : $white);
            }
        }

        imagedestroy($big);

        $path = storage_path('app/public/tmp_denoised_' . uniqid() . '.png');
        imagepng($out, $path);
        imagedestroy($out);

        return $path;
    }

    private function loadImage(string $path)
    {
        $info = getimagesize($path);
        return match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png'  => imagecreatefrompng($path),
            default      => throw new \Exception('Unsupported image format. Please upload JPG or PNG.'),
        };
    }

    // =========================================================================
    // VALIDATION
    // =========================================================================

    private function isValidSPMResult(string $text): bool
    {
        $text = strtoupper($text);
        foreach (['SIJIL', 'PELAJARAN', 'LEMBAGA', 'PEPERIKSAAN', 'KEMENTERIAN'] as $kw) {
            if (str_contains($text, $kw)) return true;
        }
        return false;
    }

    // =========================================================================
    // PARSING
    //
    // Two layout strategies:
    //
    // Layout A (plain slip — Image 1):
    //   Subject and grade are on the SAME line.
    //   e.g. "1103  BAHASA MELAYU  A+"
    //   → Scan each line, extract grade at end, fuzzy-match subject from rest.
    //
    // Layout B (certificate — Image 2):
    //   Subjects appear as a LEFT block, grades as a RIGHT block.
    //   Tesseract reads top-to-bottom so outputs all subjects then all grades.
    //   → Collect all subjects, collect all grades, zip them 1:1.
    //
    // We run BOTH and return whichever gives more subjects.
    // =========================================================================

    private function parseSPMGradesFromText(string $text): array
    {
        $text = strtoupper($text);

        // Fix common OCR grade misreads
        $text = preg_replace('/\bA[\*@®\?]\b/', 'A+', $text);
        $text = preg_replace('/\b(AT|AS)\b/', 'A+', $text);

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_values(array_filter(
            array_map(fn($l) => trim(preg_replace('/\s+/', ' ', $l)), $lines),
            fn($l) => strlen($l) > 1
        ));

        $gradesA = $this->parseLayoutA($lines);
        $gradesB = $this->parseLayoutB($lines);

        $grades = count($gradesA) >= count($gradesB) ? $gradesA : $gradesB;

        return [
            'grades'     => $grades,
            'total_as'   => $this->countAsFromGrades($grades),
            'confidence' => $this->calculateConfidence($grades, $lines),
        ];
    }

    /**
     * Layout A — grade at end of line, subject at start.
     * Handles: "1103 BAHASA MELAYU A+" and "BAHASA MELAYU A+"
     */
    private function parseLayoutA(array $lines): array
    {
        $grades      = [];
        $gradePattern = '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/';

        foreach ($lines as $line) {
            // Strip leading subject code e.g. "1103 "
            $line = preg_replace('/^\d{3,4}\s+/', '', $line);

            // Must contain a grade token
            if (!preg_match($gradePattern, $line)) continue;

            // Take the LAST grade token on the line
            preg_match_all($gradePattern, $line, $m);
            $grade = end($m[1]);

            // Subject is everything before the first grade token
            $subjectRaw = trim(preg_replace($gradePattern, '', $line));
            // Remove description words that appear after the grade on cert slips
            $subjectRaw = preg_replace('/\b(CEMERLANG|TERTINGGI|TINGGI|KEPUJIAN|LULUS|ATAS)\b.*/', '', $subjectRaw);
            $subjectRaw = trim($subjectRaw);

            $subject = $this->fuzzyMatchSubject($subjectRaw);

            if ($subject && !isset($grades[$subject])) {
                $grades[$subject] = $grade;
            }
        }

        return $grades;
    }

    /**
     * Layout B — subjects block then grades block (two-column cert).
     * Collect all subjects in order, collect all grades in order, zip.
     */
    private function parseLayoutB(array $lines): array
    {
        $subjects = [];
        $gradeList = [];

        foreach ($lines as $line) {
            $clean = preg_replace('/^\d{3,4}\s+/', '', $line);
            $clean = preg_replace('/\b(CEMERLANG|TERTINGGI|TINGGI|KEPUJIAN|LULUS|ATAS)\b.*/', '', $clean);
            $clean = trim($clean);

            $subject = $this->fuzzyMatchSubject($clean);
            if ($subject && !in_array($subject, $subjects)) {
                $subjects[] = $subject;
            }

            preg_match_all('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $line, $m);
            foreach ($m[1] as $g) {
                $gradeList[] = $g;
            }
        }

        $grades = [];
        $count  = min(count($subjects), count($gradeList));
        for ($i = 0; $i < $count; $i++) {
            $grades[$subjects[$i]] = $gradeList[$i];
        }

        return $grades;
    }

    /**
     * Fuzzy match raw OCR string to nearest known subject.
     * Accepts match if ≥ 70% similar.
     */
    private function fuzzyMatchSubject(string $raw): ?string
    {
        $raw = trim($raw);
        if (strlen($raw) < 4) return null;

        $best     = null;
        $bestPct  = 0;

        foreach (self::SUBJECTS as $subject) {
            similar_text(strtoupper($raw), $subject, $pct);
            if ($pct > $bestPct) {
                $bestPct = $pct;
                $best    = $subject;
            }
        }

        return $bestPct >= 70 ? $best : null;
    }

    // =========================================================================
    // CONFIDENCE / ACCURACY SCORE
    // Shown to user so they know how much to trust the OCR result
    //
    // Formula:
    //   50% — subject accuracy  (found vs expected from "JUMLAH MATA PELAJARAN")
    //   30% — subject clarity   (how cleanly subjects matched known list)
    //   20% — grade sanity      (penalise if all grades identical = OCR loop bug)
    // =========================================================================

    private function calculateConfidence(array $grades, array $lines): array
    {
        $matched  = count($grades);
        $expected = $this->extractExpectedSubjectCount($lines);

        // Subject accuracy
        if ($expected > 0) {
            $subjectAccuracy = min(100, (int) round(($matched / $expected) * 100));
        } else {
            $subjectAccuracy = $matched >= 5 ? min(100, (int) round($matched / 9 * 100)) : 40;
        }

        // Grade sanity — all same grade = likely OCR error
        $uniqueGrades = count(array_unique(array_values($grades)));
        $gradeSanity  = ($matched > 1 && $uniqueGrades === 1) ? 50 : 100;

        // Subject clarity — are detected subjects in our known list?
        $subjectClarity = $matched > 0
            ? (int) round(
                collect(array_keys($grades))
                    ->map(fn($s) => in_array($s, self::SUBJECTS) ? 100 : 70)
                    ->average()
              )
            : 0;

        $overall = (int) round(
            ($subjectAccuracy * 0.50) +
            ($subjectClarity  * 0.30) +
            ($gradeSanity     * 0.20)
        );

        $overall = min(100, $overall);

        return [
            'overall'           => $overall,
            'subjects_found'    => $matched,
            'subjects_expected' => $expected,
            'subject_accuracy'  => $subjectAccuracy,
            'grade_sanity'      => $gradeSanity,
            'subject_clarity'   => $subjectClarity,
            'label'             => match (true) {
                $overall >= 85 => 'High',
                $overall >= 60 => 'Medium',
                default        => 'Low',
            },
        ];
    }

    private function extractExpectedSubjectCount(array $lines): int
    {
        $numberWords = [
            'SATU' => 1, 'DUA' => 2, 'TIGA' => 3, 'EMPAT' => 4,
            'LIMA' => 5, 'ENAM' => 6, 'TUJUH' => 7, 'LAPAN' => 8,
            'SEMBILAN' => 9, 'SEPULUH' => 10, 'SEBELAS' => 11, 'DUA BELAS' => 12,
        ];

        foreach ($lines as $line) {
            $upper = strtoupper($line);

            // "JUMLAH MATA PELAJARAN : 9"
            if (preg_match('/JUMLAH.*PELAJARAN[:\s]+(\d+)/i', $line, $m)) {
                return (int) $m[1];
            }

            // "JUMLAH MATA PELAJARAN SEMBILAN"
            if (str_contains($upper, 'JUMLAH')) {
                foreach ($numberWords as $word => $num) {
                    if (str_contains($upper, $word)) return $num;
                }
            }
        }

        return 0;
    }

    // =========================================================================
    // REMAINING ENDPOINTS (unchanged)
    // =========================================================================

    public function updateOCRResults(Request $request)
    {
        $request->validate([
            'grades'   => 'required|array',
            'grades.*' => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-,D,E,G',
        ]);

        $tempData = Session::get('ocr_temp_data');
        if (!$tempData) return response()->json(['success' => false, 'message' => 'No OCR data found.'], 400);

        $updatedGrades = [];
        foreach ($request->grades as $subject => $grade) {
            if (in_array($subject, $tempData['detected_subjects'])) {
                $updatedGrades[$subject] = $grade;
            }
        }
        foreach ($tempData['detected_subjects'] as $subject) {
            if (!isset($updatedGrades[$subject])) {
                $updatedGrades[$subject] = $tempData['raw_grades'][$subject] ?? 'C';
            }
        }

        $totalAs                 = $this->countAsFromGrades($updatedGrades);
        $tempData['grades']      = $updatedGrades;
        $tempData['total_as']    = $totalAs;
        $tempData['user_edited'] = true;

        Session::put('ocr_temp_data', $tempData);

        return response()->json(['success' => true, 'message' => 'Grades updated!', 'totalAs' => $totalAs, 'updatedGrades' => $updatedGrades]);
    }

    public function verifyOCRResults(Request $request)
    {
        $request->validate(['confirm' => 'required|boolean']);

        if (!$request->confirm) {
            Session::forget('ocr_temp_data');
            return response()->json(['success' => true, 'message' => 'OCR data cleared.', 'redirect' => route('scholarship.finder')]);
        }

        $tempData = Session::get('ocr_temp_data');
        if (!$tempData) return response()->json(['success' => false, 'message' => 'No data to verify.'], 400);

        Session::put('verified_ocr_data', [
            'grades'            => $tempData['grades'],
            'total_as'          => $tempData['total_as'],
            'detected_subjects' => $tempData['detected_subjects'],
            'confidence'        => $tempData['confidence'] ?? null,
            'verified_at'       => now(),
        ]);

        Session::forget('ocr_temp_data');
        return response()->json(['success' => true, 'message' => 'SPM results verified!', 'totalAs' => $tempData['total_as']]);
    }

    public function addSubject(Request $request)
    {
        $request->validate(['subject' => 'required|string|max:100', 'grade' => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-,D,E,G']);

        $tempData = Session::get('ocr_temp_data');
        if (!$tempData) return response()->json(['success' => false, 'message' => 'No OCR session found.'], 400);

        $tempData['grades'][$request->subject] = $request->grade;
        if (!in_array($request->subject, $tempData['detected_subjects'])) {
            $tempData['detected_subjects'][] = $request->subject;
        }

        $totalAs              = $this->countAsFromGrades($tempData['grades']);
        $tempData['total_as'] = $totalAs;
        Session::put('ocr_temp_data', $tempData);

        return response()->json(['success' => true, 'message' => 'Subject added!', 'totalAs' => $totalAs]);
    }

    public function removeSubject(Request $request)
    {
        $request->validate(['subject' => 'required|string']);

        $tempData = Session::get('ocr_temp_data');
        if (!$tempData) return response()->json(['success' => false, 'message' => 'No OCR session found.'], 400);

        unset($tempData['grades'][$request->subject]);
        $tempData['detected_subjects'] = array_values(array_filter(
            $tempData['detected_subjects'], fn($s) => $s !== $request->subject
        ));

        $totalAs              = $this->countAsFromGrades($tempData['grades']);
        $tempData['total_as'] = $totalAs;
        Session::put('ocr_temp_data', $tempData);

        return response()->json(['success' => true, 'message' => 'Subject removed!', 'totalAs' => $totalAs]);
    }

    private function countAsFromGrades(array $grades): int
    {
        return collect($grades)->filter(fn($g) => str_starts_with($g, 'A'))->count();
    }
}