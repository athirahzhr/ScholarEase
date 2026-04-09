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
                'academic_category' => $results['academic_category'],
                'detected_subjects' => array_keys($results['grades']),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'grades' => $results['grades'],
                'totalAs' => $results['total_as'],
                'academicCategory' => $results['academic_category'],
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
            ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe')
            ->lang('eng')
            ->run();

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

        imagefilter($image, IMG_FILTER_GRAYSCALE);
        imagefilter($image, IMG_FILTER_CONTRAST, -20);

        $processedPath = storage_path('app/public/temp_processed.jpg');

        imagejpeg($image, $processedPath, 100);

        imagedestroy($image);

        return $processedPath;
    }

    private function isValidSPMResult($text)
    {
        $keywords = [
            'KEMENTERIAN PENDIDIKAN',
            'LEMBAGA PEPERIKSAAN',
            'SIJIL PELAJARAN MALAYSIA'
        ];

        $matches = 0;

        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $matches++;
            }
        }

        return $matches >= 2;
    }

    private function parseSPMGradesFromText($text)
    {
        $subjects = [
            'BAHASA MELAYU',
            'BAHASA INGGERIS',
            'SEJARAH',
            'MATEMATIK',
            'MATHEMATICS',
            'SAINS',
            'BIOLOGI',
            'BIOLOGY',
            'FIZIK',
            'PHYSICS',
            'KIMIA',
            'CHEMISTRY',
            'MATEMATIK TAMBAHAN',
            'ADDITIONAL MATHEMATICS',
            'PENDIDIKAN ISLAM',
            'PENDIDIKAN MORAL',
            'BAHASA ARAB',
            'PENDIDIKAN AL-QURAN DAN AL-SUNNAH',
            'PENDIDIKAN SYARIAH ISLAMIAH',
            'PRINSIP PERAKAUNAN',
            'EKONOMI',
            'PERDAGANGAN',
            'GEOGRAFI'
        ];

        $grades = [];
        $normalizedText = strtoupper($text);

        foreach ($subjects as $subject) {
            $pattern = '/' .
                preg_quote($subject, '/') .
                '[\s\r\n]{0,20}' .
                '(A\+|A-|A|B\+|B-|B|C\+|C-|C|D|E|G)/i';

            if (preg_match($pattern, $normalizedText, $matches)) {
                $grades[$subject] = strtoupper($matches[1]);
            }
        }

        if (count($grades) < 5) {
            throw new \Exception('Too few subjects detected. Invalid or unclear SPM slip.');
        }

        $totalAs = $this->countAsFromGrades($grades);

        return [
            'grades' => $grades,
            'total_as' => $totalAs,
            'academic_category' => $this->determineAcademicCategory($totalAs)
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
        $academicCategory = $this->determineAcademicCategory($totalAs);

        $tempData['grades'] = $updatedGrades;
        $tempData['total_as'] = $totalAs;
        $tempData['academic_category'] = $academicCategory;
        $tempData['user_edited'] = true;

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Grades updated successfully!',
            'totalAs' => $totalAs,
            'academicCategory' => $academicCategory,
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
            'academic_category' => $tempData['academic_category'],
            'detected_subjects' => $tempData['detected_subjects'],
            'verified_at' => now()
        ]);

        Session::forget('ocr_temp_data');

        return response()->json([
            'success' => true,
            'message' => 'SPM results verified successfully!',
            'academicCategory' => $tempData['academic_category'],
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
        $tempData['academic_category'] = $this->determineAcademicCategory($totalAs);

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Subject added successfully!',
            'totalAs' => $totalAs,
            'academicCategory' => $tempData['academic_category']
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
        $tempData['academic_category'] = $this->determineAcademicCategory($totalAs);

        Session::put('ocr_temp_data', $tempData);

        return response()->json([
            'success' => true,
            'message' => 'Subject removed successfully!',
            'totalAs' => $totalAs,
            'academicCategory' => $tempData['academic_category']
        ]);
    }

    private function countAsFromGrades($grades)
    {
        return collect($grades)->filter(function ($grade) {
            return str_starts_with($grade, 'A');
        })->count();
    }

    private function determineAcademicCategory($totalAs)
    {
        if ($totalAs >= 10) return 'A4';
        if ($totalAs >= 7) return 'A3';
        if ($totalAs >= 4) return 'A2';
        return 'A1';
    }
}