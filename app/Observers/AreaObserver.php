<?php

namespace App\Observers;

use App\Models\Area;
use Illuminate\Support\Facades\Cache;

class AreaObserver
{
    public function saved(Area $area): void
    {
        Cache::forget("areas_{$area->city_id}");
    }

    public function deleted(Area $area): void
    {
        Cache::forget("areas_{$area->city_id}");
    }
}
