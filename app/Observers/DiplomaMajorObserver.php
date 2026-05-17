<?php

namespace App\Observers;

use App\Models\DiplomaMajor;
use Illuminate\Support\Facades\Cache;

class DiplomaMajorObserver
{
    public function saved(DiplomaMajor $diplomaMajor): void
    {
        Cache::forget("diploma_majors_{$diplomaMajor->diploma_id}");
    }

    public function deleted(DiplomaMajor $diplomaMajor): void
    {
        Cache::forget("diploma_majors_{$diplomaMajor->diploma_id}");
    }
}
