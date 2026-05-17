<?php

namespace App\Observers;

use App\Models\SvgCategory;
use Illuminate\Support\Facades\Cache;

class SvgCategoryObserver
{
    public function saved(SvgCategory $_svgCategory): void
    {
        Cache::forget('svg_categories');
    }

    public function deleted(SvgCategory $_svgCategory): void
    {
        Cache::forget('svg_categories');
    }
}
