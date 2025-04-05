<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\FileUploadForm;
use App\Livewire\FileDownloadForm;
use App\Http\Controllers\FileDownloadController;

Route::get('/upload', FileUploadForm::class);
Route::get('/download', FileDownloadForm::class);
Route::get('/file/download/{uuid}', [FileDownloadController::class, 'download'])->name('file.download');
