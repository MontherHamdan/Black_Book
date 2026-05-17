<?php

namespace App\Observers;

use App\Models\Governorate;
use Illuminate\Support\Facades\Cache;

class GovernorateObserver
{
    public function saved(Governorate $governorate): void
    {
        Cache::forget('location_governorates');
    }

    public function deleted(Governorate $governorate): void
    {
        Cache::forget('location_governorates');
    }
}
