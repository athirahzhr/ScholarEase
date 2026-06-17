<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRController extends Controller
{
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
                'detected_subjects' => array_keys($results['grades']),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'grades' => $results['grades'],
                'totalAs' => $results['total_as'],
                'detectedSubjects' => array_keys($results['grades']),
                'message' => 'SPM results extracted successfully!',
                'allowEdit' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process SPM: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processRealOCR($path)
{
    $fullPath = storage_path('app/public/' . $path);

    $processedPath = $this->preprocessImage($fullPath);

    $text = (new TesseractOCR($processedPath))
        ->executable('/usr/bin/tesseract')
        ->lang('eng')
        ->run();

    // Debug OCR output
    file_put_contents(
        storage_path('app/ocr_debug.txt'),
        $text
    );

    if (!$this->isValidSPMResult($text)) {
        throw new \Exception('Uploaded file is not a valid SPM result slip.');
    }

    return $this->parseSPMGradesFromText($text);
}

    private function preprocessImage($fullPath)
{
    $imageInfo = getimagesize($fullPath);

    switch ($imageInfo['mime']) {

        case 'image/jpeg':
            $image = imagecreatefromjpeg($fullPath);
            break;

        case 'image/png':
            $image = imagecreatefrompng($fullPath);
            break;

        default:
            throw new \Exception('Unsupported image format.');
    }

    $width = imagesx($image);
    $height = imagesy($image);

    // Besarkan gambar untuk OCR
    $newWidth = $width * 3;
    $newHeight = $height * 3;

    $resized = imagecreatetruecolor($newWidth, $newHeight);

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

    $image = $resized;

    imagefilter($image, IMG_FILTER_GRAYSCALE);
    imagefilter($image, IMG_FILTER_CONTRAST, -40);
    imagefilter($image, IMG_FILTER_BRIGHTNESS, 10);

    $processedPath = storage_path(
        'app/public/temp_' . uniqid() . '.jpg'
    );

    imagejpeg($image, $processedPath, 100);

    imagedestroy($image);

    return $processedPath;
}

    private function isValidSPMResult($text)
{
    $text = strtoupper($text);

    return (
        str_contains($text, 'SIJIL') ||
        str_contains($text, 'PELAJARAN') ||
        str_contains($text, 'LEMBAGA') ||
        str_contains($text, 'PEPERIKSAAN')
    );
}

    private function parseSPMGradesFromText($text)
{
    $subjects = [
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
        'GEOGRAFI'
    ];

    $text = strtoupper($text);

    // Betulkan OCR typo biasa
    $text = str_replace([
        'AT',
        'AS',
        'A®',
        'A?',
        'A*'
    ], 'A+', $text);

    $lines = preg_split('/\r\n|\r|\n/', $text);

    $lines = array_map(function ($line) {
        return trim(preg_replace('/\s+/', ' ', $line));
    }, $lines);

    $detectedSubjects = [];

    foreach ($lines as $line) {

        foreach ($subjects as $subject) {

            if (
                stripos($line, $subject) !== false &&
                !in_array($subject, $detectedSubjects)
            ) {
                $detectedSubjects[] = $subject;
            }
        }
    }

    preg_match_all(
        '/\b(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)\b/',
        $text,
        $gradeMatches
    );

    $detectedGrades = $gradeMatches[1] ?? [];

    $grades = [];

    $count = min(
        count($detectedSubjects),
        count($detectedGrades)
    );

    for ($i = 0; $i < $count; $i++) {

        $grades[$detectedSubjects[$i]] =
            strtoupper($detectedGrades[$i]);
    }

    if (count($grades) < 3) {
        throw new \Exception(
            'Unable to detect enough subjects from SPM slip.'
        );
    }

    return [
        'grades' => $grades,
        'total_as' => $this->countAsFromGrades($grades)
    ];
}

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
        

        $tempData['grades'] = $updatedGrades;
        $tempData['total_as'] = $totalAs;
        $tempData['user_edited'] = true;

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Grades updated successfully!',
            'totalAs' => $totalAs,
            'updatedGrades' => $updatedGrades
        ]);
    }

    public function verifyOCRResults(Request $request)
    {
        $request->validate([
            'confirm' => 'required|boolean'
        ]);

        if (!$request->confirm) {
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
            'verified_at' => now()
        ]);

        Session::forget('ocr_temp_data');

        return response()->json([
            'success' => true,
            'message' => 'SPM results verified successfully!',
            'totalAs' => $tempData['total_as']
        ]);
    }

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

        $tempData['grades'][$request->subject] = $request->grade;

        if (!in_array($request->subject, $tempData['detected_subjects'])) {
            $tempData['detected_subjects'][] = $request->subject;
        }

        $totalAs = $this->countAsFromGrades($tempData['grades']);

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

        unset($tempData['grades'][$request->subject]);

        $tempData['detected_subjects'] = array_values(array_filter(
            $tempData['detected_subjects'],
            fn($s) => $s !== $request->subject
        ));

        $totalAs = $this->countAsFromGrades($tempData['grades']);

        $tempData['total_as'] = $totalAs;
        

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Subject removed successfully!',
            'totalAs' => $totalAs,
        
        ]);
    }

    private function countAsFromGrades($grades)
    {
        return collect($grades)->filter(function ($grade) {
            return str_starts_with($grade, 'A');
        })->count();
    }

}