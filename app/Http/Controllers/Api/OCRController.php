<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrController extends Controller
{
    public function uploadSPM(Request $request)
    { 
        $request->validate([
            'spm_file' => 'required|file|mimes:jpg,jpeg,png|max:5120'
        ]);

        try {
            $user = $request->user();
            $file = $request->file('spm_file');

            $filename = 'spm_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('spm_documents', $filename, 'public');

            $result = $this->processOCR($path);

            return response()->json([
                'success' => true,
                'file_path' => $path,
                'grades' => $result['grades'],
                'total_as' => $result['total_as'],
                'academic_category' => $result['academic_category'],
                'allow_edit' => true,
                'message' => 'OCR completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function processOCR($path)
    {
        $fullPath = storage_path('app/public/' . $path);

        $text = (new TesseractOCR($fullPath))
            ->lang('eng')
            ->run();

        if (!$this->isValidSPM($text)) {
            throw new \Exception("Invalid SPM document");
        }

        return $this->parseGrades($text);
    }

    private function isValidSPM($text)
    {
        return str_contains(strtoupper($text), 'SIJIL PELAJARAN MALAYSIA');
    }

    private function parseGrades($text)
    {
        $subjects = [
            'BAHASA MELAYU',
            'BAHASA INGGERIS',
            'MATEMATIK',
            'SEJARAH',
            'SAINS',
            'BIOLOGI',
            'FIZIK',
            'KIMIA'
        ];

        $grades = [];
        $text = strtoupper($text);

        foreach ($subjects as $subject) {
            if (preg_match('/' . preg_quote($subject, '/') . '.*?(A\+|A|A-|B\+|B|C|D|E|G)/', $text, $m)) {
                $grades[$subject] = $m[1];
            }
        }

        return [
            'grades' => $grades,
            'total_as' => $this->countA($grades),
            'academic_category' => $this->category($this->countA($grades))
        ];
    }

    public function updateOCR(Request $request)
    {
        $request->validate([
            'grades' => 'required|array'
        ]);

        $grades = $request->grades;

        $totalAs = $this->countA($grades);

        return response()->json([
            'success' => true,
            'grades' => $grades,
            'total_as' => $totalAs,
            'academic_category' => $this->category($totalAs)
        ]);
    }

    private function countA($grades)
    {
    return collect($grades)->filter(fn($g) => str_starts_with($g, 'A'))->count();
    }

    private function category($a)
    {
        if ($a >= 10) return 'A4';
        if ($a >= 7) return 'A3';
        if ($a >= 4) return 'A2';
        return 'A1';
    }
}
