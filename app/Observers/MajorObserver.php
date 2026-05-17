<?php

namespace App\Observers;

use App\Models\Major;
use Illuminate\Support\Facades\Cache;

class MajorObserver
{
    public function saved(Major $major): void
    {
        Cache::forget("majors_{$major->university_id}");
    }

    public function deleted(Major $major): void
    {
        Cache::forget("majors_{$major->university_id}");
    }
}
