<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRController extends Controller
{
    // =========================================================================
    // Known SPM subjects — used for fuzzy matching
    // =========================================================================
    private const SUBJECTS = [
        'BAHASA MELAYU',
        'BAHASA INGGERIS',
        'BAHASA INGGERIS (1119)',
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
        'PENDIDIKAN JASMANI DAN KESIHATAN',
        'REKA CIPTA',
        'ELEKTIF VOKASIONAL',
    ];

    // Valid SPM grades in order
    private const GRADES = ['A+', 'A-', 'A', 'B+', 'B-', 'B', 'C+', 'C-', 'C', 'D', 'E', 'G'];

    // =========================================================================
    // PUBLIC: Upload & process SPM image
    // =========================================================================

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
                'message' => 'Failed to process SPM: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // CORE: OCR pipeline
    // =========================================================================

    private function processRealOCR(string $path): array
    {
        $fullPath = storage_path('app/public/' . $path);

        // Run two preprocessing strategies in parallel and pick the better result
        $cleanPath      = $this->preprocessClean($fullPath);
        $denoisedPath   = $this->preprocessDenoised($fullPath);

        $textClean    = $this->runTesseract($cleanPath);
        $textDenoised = $this->runTesseract($denoisedPath);

        // Write both for debugging
        file_put_contents(storage_path('app/ocr_debug_clean.txt'),    $textClean);
        file_put_contents(storage_path('app/ocr_debug_denoised.txt'), $textDenoised);

        // Pick the OCR pass that detected more subjects
        $resultClean    = $this->parseSPMGradesFromText($textClean,    false);
        $resultDenoised = $this->parseSPMGradesFromText($textDenoised, false);

        $result = count($resultClean['grades']) >= count($resultDenoised['grades'])
            ? $resultClean
            : $resultDenoised;

        // Cleanup temp files
        @unlink($cleanPath);
        @unlink($denoisedPath);

        if (count($result['grades']) < 3) {
            throw new \Exception('Unable to detect enough subjects. Please upload a clearer image of your SPM result slip.');
        }

        // Validate it looks like an SPM slip
        $combinedText = $textClean . ' ' . $textDenoised;
        if (!$this->isValidSPMResult($combinedText)) {
            throw new \Exception('Uploaded file does not appear to be a valid SPM result slip.');
        }

        return $result;
    }

    private function runTesseract(string $path): string
    {
        return (new TesseractOCR($path))
            ->executable('/usr/bin/tesseract')
            ->lang('eng+msa')          // English + Malay language pack
            ->psm(6)                   // Assume uniform block of text — best for result slips
            ->oem(1)                   // LSTM only (most accurate)
            ->run();
    }

    // =========================================================================
    // PREPROCESSING STRATEGY 1 — Clean slip (plain white background)
    // Upscale → greyscale → high contrast → sharpen
    // Works best for Image 1 (plain result slip)
    // =========================================================================

    private function preprocessClean(string $fullPath): string
    {
        $image = $this->loadImage($fullPath);

        // Upscale 3× for better character recognition
        [$w, $h] = [imagesx($image), imagesy($image)];
        $upscaled = imagecreatetruecolor($w * 3, $h * 3);
        imagecopyresampled($upscaled, $image, 0, 0, 0, 0, $w * 3, $h * 3, $w, $h);
        imagedestroy($image);

        imagefilter($upscaled, IMG_FILTER_GRAYSCALE);
        imagefilter($upscaled, IMG_FILTER_CONTRAST, -50);    // high contrast
        imagefilter($upscaled, IMG_FILTER_BRIGHTNESS, 5);
        imagefilter($upscaled, IMG_FILTER_SMOOTH, 1);

        $path = storage_path('app/public/temp_clean_' . uniqid() . '.png');
        imagepng($upscaled, $path);
        imagedestroy($upscaled);

        return $path;
    }

    // =========================================================================
    // PREPROCESSING STRATEGY 2 — Noisy/patterned background (official cert)
    // Upscale → greyscale → adaptive threshold to kill background patterns
    // Works best for Image 2 (guilloché background certificate)
    // =========================================================================

    private function preprocessDenoised(string $fullPath): string
    {
        $image = $this->loadImage($fullPath);

        // Upscale 3×
        [$w, $h] = [imagesx($image), imagesy($image)];
        $upscaled = imagecreatetruecolor($w * 3, $h * 3);
        imagecopyresampled($upscaled, $image, 0, 0, 0, 0, $w * 3, $h * 3, $w, $h);
        imagedestroy($image);

        $newW = imagesx($upscaled);
        $newH = imagesy($upscaled);

        imagefilter($upscaled, IMG_FILTER_GRAYSCALE);

        // Adaptive threshold — converts each pixel to pure black or white
        // based on local neighbourhood brightness average.
        // This kills the guilloché pattern which is mid-grey and keeps
        // dark text as black on a white background.
        $thresholded = imagecreatetruecolor($newW, $newH);
        $white       = imagecolorallocate($thresholded, 255, 255, 255);
        $black       = imagecolorallocate($thresholded, 0, 0, 0);
        imagefill($thresholded, 0, 0, $white);

        $blockSize = 31;  // neighbourhood size — odd number
        $C         = 10;  // constant subtracted from mean (tune if needed)

        for ($y = 0; $y < $newH; $y++) {
            for ($x = 0; $x < $newW; $x++) {

                // Gather local neighbourhood
                $sum   = 0;
                $count = 0;
                $half  = (int) ($blockSize / 2);

                for ($dy = -$half; $dy <= $half; $dy++) {
                    for ($dx = -$half; $dx <= $half; $dx++) {
                        $nx = max(0, min($newW - 1, $x + $dx));
                        $ny = max(0, min($newH - 1, $y + $dy));
                        $rgb = imagecolorat($upscaled, $nx, $ny);
                        $sum += ($rgb >> 16) & 0xFF; // red channel = grey
                        $count++;
                    }
                }

                $mean     = $sum / $count;
                $pixel    = imagecolorat($upscaled, $x, $y);
                $grey     = ($pixel >> 16) & 0xFF;

                // Pixel is "ink" if significantly darker than local mean
                imagesetpixel($thresholded, $x, $y,
                    $grey < ($mean - $C) ? $black : $white
                );
            }
        }

        imagedestroy($upscaled);

        // Mild erosion pass to thicken thin characters after threshold
        imagefilter($thresholded, IMG_FILTER_SMOOTH, -1);

        $path = storage_path('app/public/temp_denoised_' . uniqid() . '.png');
        imagepng($thresholded, $path);
        imagedestroy($thresholded);

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

        $keywords = ['SIJIL', 'PELAJARAN', 'LEMBAGA', 'PEPERIKSAAN', 'KEMENTERIAN'];

        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) {
                return true;
            }
        }

        return false;
    }

    // =========================================================================
    // PARSING — column-aware subject+grade extraction
    //
    // SPM slips have two layouts:
    //   A) Plain slip (Image 1)  — subjects on LEFT, grades on RIGHT of same line
    //   B) Certificate (Image 2) — subjects block on left half, grades block on right half
    //      OCR reads left-to-right, top-to-bottom so grades appear AFTER all subjects.
    //
    // Strategy:
    //   1. Find all subject names (fuzzy match against known list)
    //   2. Find all grade tokens
    //   3. If #subjects ≈ #grades AND interleaved → layout A (zip them)
    //   4. Otherwise → layout B (subjects first, then grades block)
    //   5. Calculate confidence = matched / expected subjects
    // =========================================================================

    private function parseSPMGradesFromText(string $text, bool $throwOnFail = true): array
    {
        $text = strtoupper($text);

        // Normalise OCR common mistakes for grades
        $text = preg_replace('/\bA[\*@®\?]\b/', 'A+', $text);
        $text = str_replace(['AT ', 'AS ', 'A® ', 'A? '], 'A+ ', $text);

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_map(fn($l) => trim(preg_replace('/\s+/', ' ', $l)), $lines);
        $lines = array_filter($lines, fn($l) => strlen($l) > 0);
        $lines = array_values($lines);

        // ── Pass 1: Try line-by-line match (Layout A — plain slip) ────────────
        $gradesLayoutA = $this->parseLayoutA($lines);

        // ── Pass 2: Two-column block match (Layout B — certificate) ───────────
        $gradesLayoutB = $this->parseLayoutB($lines);

        // Pick whichever extracted more subjects
        $grades = count($gradesLayoutA) >= count($gradesLayoutB)
            ? $gradesLayoutA
            : $gradesLayoutB;

        if ($throwOnFail && count($grades) < 3) {
            throw new \Exception('Unable to detect enough subjects from the SPM slip.');
        }

        $totalAs    = $this->countAsFromGrades($grades);
        $confidence = $this->calculateConfidence($grades, $lines);

        return [
            'grades'     => $grades,
            'total_as'   => $totalAs,
            'confidence' => $confidence,
        ];
    }

    /**
     * Layout A — subject and grade appear on the SAME line.
     * e.g. "BAHASA MELAYU  A+"  or  "1103  BAHASA MELAYU  A+"
     */
    private function parseLayoutA(array $lines): array
    {
        $grades = [];

        foreach ($lines as $line) {
            // Remove subject codes (4-digit numbers at line start)
            $line = preg_replace('/^\d{4}\s+/', '', $line);

            // Try to find a grade token at the END of the line
            if (preg_match('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b\s*$/', $line, $gradeMatch)) {
                $grade       = $gradeMatch[1];
                $subjectPart = trim(preg_replace('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b\s*$/', '', $line));

                $subject = $this->fuzzyMatchSubject($subjectPart);

                if ($subject && !isset($grades[$subject])) {
                    $grades[$subject] = $grade;
                }
            }
        }

        return $grades;
    }

    /**
     * Layout B — subjects appear as a block, grades appear as a separate block.
     * Tesseract reads left-to-right so grades come AFTER subjects in the text.
     * We collect all subjects first, then all grades, then zip them together.
     */
    private function parseLayoutB(array $lines): array
    {
        $detectedSubjects = [];
        $detectedGrades   = [];

        foreach ($lines as $line) {
            // Remove subject codes
            $clean = preg_replace('/^\d{4}\s+/', '', $line);

            // Check if this line is purely a subject name
            $matchedSubject = $this->fuzzyMatchSubject($clean);
            if ($matchedSubject && !in_array($matchedSubject, $detectedSubjects)) {
                $detectedSubjects[] = $matchedSubject;
            }

            // Extract any grade tokens from this line
            preg_match_all('/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/', $line, $gradeMatches);
            foreach ($gradeMatches[1] as $g) {
                $detectedGrades[] = $g;
            }
        }

        // Zip subjects to grades (1-to-1)
        $grades = [];
        $count  = min(count($detectedSubjects), count($detectedGrades));

        for ($i = 0; $i < $count; $i++) {
            $grades[$detectedSubjects[$i]] = $detectedGrades[$i];
        }

        return $grades;
    }

    /**
     * Fuzzy-match a raw OCR string to the nearest known subject name.
     * Uses similar_text() — good for OCR noise like 'MATEMAT1K' vs 'MATEMATIK'.
     */
    private function fuzzyMatchSubject(string $raw): ?string
    {
        $raw = trim($raw);

        if (strlen($raw) < 4) {
            return null;
        }

        $bestSubject = null;
        $bestScore   = 0;

        foreach (self::SUBJECTS as $subject) {
            similar_text(strtoupper($raw), $subject, $pct);

            if ($pct > $bestScore) {
                $bestScore   = $pct;
                $bestSubject = $subject;
            }
        }

        // Only accept if similarity is above 70% to avoid false matches
        return $bestScore >= 70 ? $bestSubject : null;
    }

    // =========================================================================
    // ACCURACY / CONFIDENCE SCORE
    // =========================================================================

    /**
     * Calculate OCR confidence percentage to show the user.
     *
     * Formula:
     *   confidence = (subjects_matched / expected_subjects) × 100
     *
     * expected_subjects comes from "JUMLAH MATA PELAJARAN : X" line if present,
     * otherwise we assume the number of subjects we found is reasonable.
     *
     * We also penalise if grades look suspicious (e.g. all same grade).
     */
    private function calculateConfidence(array $grades, array $lines): array
    {
        $matched  = count($grades);
        $expected = $this->extractExpectedSubjectCount($lines);

        // Base accuracy
        if ($expected > 0) {
            $subjectAccuracy = min(100, round(($matched / $expected) * 100));
        } else {
            // No "JUMLAH" line found — estimate based on reasonable SPM range (5–12)
            $subjectAccuracy = $matched >= 5 ? min(100, round($matched / 9 * 100)) : 40;
        }

        // Grade sanity check — penalise if ALL grades are the same (likely OCR looping)
        $uniqueGrades    = count(array_unique(array_values($grades)));
        $gradeSanity     = $matched > 1 && $uniqueGrades === 1 ? 60 : 100;

        // Subject name clarity — how many subjects matched with high similarity
        $subjectClarity  = $matched > 0
            ? round($this->averageSubjectSimilarity($grades) * 100)
            : 0;

        // Final weighted score
        $overall = (int) round(
            ($subjectAccuracy * 0.50) +
            ($subjectClarity  * 0.30) +
            ($gradeSanity     * 0.20)
        );

        return [
            'overall'          => min(100, $overall),
            'subjects_found'   => $matched,
            'subjects_expected'=> $expected,
            'subject_accuracy' => $subjectAccuracy,
            'grade_sanity'     => $gradeSanity,
            'subject_clarity'  => $subjectClarity,
            'label'            => match(true) {
                $overall >= 85 => 'High',
                $overall >= 60 => 'Medium',
                default        => 'Low — please review and edit the results',
            },
        ];
    }

    private function extractExpectedSubjectCount(array $lines): int
    {
        foreach ($lines as $line) {
            // Matches "JUMLAH MATA PELAJARAN : 9" or "JUMLAH MATA PELAJARAN SEMBILAN"
            if (preg_match('/JUMLAH.*PELAJARAN[:\s]+(\d+)/i', $line, $m)) {
                return (int) $m[1];
            }
            // Malay number words as fallback
            $words = [
                'SATU' => 1, 'DUA' => 2, 'TIGA' => 3, 'EMPAT' => 4,
                'LIMA' => 5, 'ENAM' => 6, 'TUJUH' => 7, 'LAPAN' => 8,
                'SEMBILAN' => 9, 'SEPULUH' => 10, 'SEBELAS' => 11, 'DUA BELAS' => 12,
            ];
            foreach ($words as $word => $num) {
                if (str_contains(strtoupper($line), 'JUMLAH') && str_contains(strtoupper($line), $word)) {
                    return $num;
                }
            }
        }

        return 0;
    }

    private function averageSubjectSimilarity(array $grades): float
    {
        if (empty($grades)) return 0.0;

        $total = 0.0;

        foreach (array_keys($grades) as $subject) {
            // If the subject is already in our known list → perfect match
            $total += in_array($subject, self::SUBJECTS) ? 1.0 : 0.7;
        }

        return $total / count($grades);
    }

    // =========================================================================
    // REST OF EXISTING METHODS (unchanged)
    // =========================================================================

    public function updateOCRResults(Request $request)
    {
        $request->validate([
            'grades'   => 'required|array',
            'grades.*' => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-,D,E,G'
        ]);

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json(['success' => false, 'message' => 'No OCR data found.'], 400);
        }

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

        $totalAs = $this->countAsFromGrades($updatedGrades);

        $tempData['grades']      = $updatedGrades;
        $tempData['total_as']    = $totalAs;
        $tempData['user_edited'] = true;

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success'       => true,
            'message'       => 'Grades updated successfully!',
            'totalAs'       => $totalAs,
            'updatedGrades' => $updatedGrades,
        ]);
    }

    public function verifyOCRResults(Request $request)
    {
        $request->validate(['confirm' => 'required|boolean']);

        if (!$request->confirm) {
            Session::forget('ocr_temp_data');

            return response()->json([
                'success'  => true,
                'message'  => 'OCR data cleared.',
                'redirect' => route('scholarship.finder'),
            ]);
        }

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json(['success' => false, 'message' => 'No data to verify.'], 400);
        }

        Session::put('verified_ocr_data', [
            'grades'            => $tempData['grades'],
            'total_as'          => $tempData['total_as'],
            'detected_subjects' => $tempData['detected_subjects'],
            'confidence'        => $tempData['confidence'] ?? null,
            'verified_at'       => now(),
        ]);

        Session::forget('ocr_temp_data');

        return response()->json([
            'success' => true,
            'message' => 'SPM results verified successfully!',
            'totalAs' => $tempData['total_as'],
        ]);
    }

    public function addSubject(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'grade'   => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-,D,E,G',
        ]);

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json(['success' => false, 'message' => 'No OCR session found.'], 400);
        }

        $tempData['grades'][$request->subject] = $request->grade;

        if (!in_array($request->subject, $tempData['detected_subjects'])) {
            $tempData['detected_subjects'][] = $request->subject;
        }

        $totalAs            = $this->countAsFromGrades($tempData['grades']);
        $tempData['total_as'] = $totalAs;

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Subject added successfully!',
            'totalAs' => $totalAs,
        ]);
    }

    public function removeSubject(Request $request)
    {
        $request->validate(['subject' => 'required|string']);

        $tempData = Session::get('ocr_temp_data');

        if (!$tempData) {
            return response()->json(['success' => false, 'message' => 'No OCR session found.'], 400);
        }

        unset($tempData['grades'][$request->subject]);

        $tempData['detected_subjects'] = array_values(array_filter(
            $tempData['detected_subjects'],
            fn($s) => $s !== $request->subject
        ));

        $totalAs              = $this->countAsFromGrades($tempData['grades']);
        $tempData['total_as'] = $totalAs;

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Subject removed successfully!',
            'totalAs' => $totalAs,
        ]);
    }

    private function countAsFromGrades(array $grades): int
    {
        return collect($grades)->filter(fn($g) => str_starts_with($g, 'A'))->count();
    }
}