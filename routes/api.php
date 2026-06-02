<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\OCRController;
use App\Http\Controllers\Api\ScholarshipController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy']);
    Route::post('/bookmarks',[BookmarkController::class, 'store']);

    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'store']);

    Route::post('/ocr/upload', [OcrController::class, 'uploadSPM']);
    Route::post('/ocr/update', [OcrController::class, 'updateOCR']);

    Route::get('/scholarships', [ScholarshipController::class, 'index']);
    Route::get('/scholarships/recommendations',[ScholarshipController::class, 'recommendations']);
    Route::get('/scholarships/{id}',[ScholarshipController::class, 'show']);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'API route not found'
    ], 404);
});
