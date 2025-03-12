<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/files', [FileUploadController::class, 'index'])->name('files.index');
Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');
Route::delete('/files/{id}', [FileUploadController::class, 'delete'])->name('file.delete');
Route::get('/upload', function () {
    return view('upload');
});
