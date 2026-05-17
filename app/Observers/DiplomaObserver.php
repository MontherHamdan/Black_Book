<?php

namespace App\Observers;

use App\Models\Diploma;
use Illuminate\Support\Facades\Cache;

class DiplomaObserver
{
    public function saved(Diploma $diploma): void
    {
        Cache::forget('diplomas');
        Cache::forget("diploma_majors_{$diploma->id}");
    }

    public function deleted(Diploma $diploma): void
    {
        Cache::forget('diplomas');
        Cache::forget("diploma_majors_{$diploma->id}");
    }
}
