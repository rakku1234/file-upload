<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\File;

class FileDownloadController extends Controller
{
    public function download($uuid)
    {
        $file = File::where('uuid', $uuid)->first();
        $files = Storage::disk('local')->files($file->directory);
        if (empty($files)) {
            abort(404);
            return;
        }
        $filePath = $files[0];
        return response()->download(
            Storage::disk('local')->path($filePath),
            $file->file_name
        );
    }
}
