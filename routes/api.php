<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/upload', [UploadController::class, 'upload']);
Route::get('/download/{token}', [UploadController::class, 'download'])->name('download');
Route::get('/uploads/stats/{token}', [UploadController::class, 'stats']);

// Optional: Protected routes if you implement authentication
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user/uploads', [UploadController::class, 'userUploads']);
// });
