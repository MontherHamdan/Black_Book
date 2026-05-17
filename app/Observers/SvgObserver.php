<?php

namespace App\Observers;

use App\Models\Svg;
use Illuminate\Support\Facades\Cache;

class SvgObserver
{
    public function saved(Svg $svg): void
    {
        Cache::forget('svg_categories');
    }

    public function deleted(Svg $svg): void
    {
        Cache::forget('svg_categories');
    }
}
