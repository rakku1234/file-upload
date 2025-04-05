<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class DirDelete implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dirs = File::where('created_at', '<', now()->subDays(3))->get();
        foreach ($dirs as $dir) {
            Storage::disk('local')->deleteDirectory($dir->directory);
            $dir->delete();
        }
    }
}
