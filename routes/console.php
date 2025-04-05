<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\DirDelete;

Schedule::job(DirDelete::class)->hourly();
