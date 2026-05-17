<?php

namespace App\Observers;

use App\Models\Major;
use App\Models\University;
use Illuminate\Support\Facades\Cache;

class UniversityObserver
{
    public function saved(University $university): void
    {
        Cache::forget('universities');
        Cache::forget("majors_{$university->id}");
    }

    public function deleted(University $university): void
    {
        Cache::forget('universities');
        Cache::forget("majors_{$university->id}");
    }
}
