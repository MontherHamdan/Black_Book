<?php

namespace App\Observers;

use App\Models\Governorate;
use Illuminate\Support\Facades\Cache;

class GovernorateObserver
{
    public function saved(Governorate $_governorate): void
    {
        Cache::forget('location_governorates');
    }

    public function deleted(Governorate $_governorate): void
    {
        Cache::forget('location_governorates');
    }
}
