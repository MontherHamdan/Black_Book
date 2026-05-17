<?php

namespace App\Observers;

use App\Models\SvgCategory;
use Illuminate\Support\Facades\Cache;

class SvgCategoryObserver
{
    public function saved(SvgCategory $svgCategory): void
    {
        Cache::forget('svg_categories');
    }

    public function deleted(SvgCategory $svgCategory): void
    {
        Cache::forget('svg_categories');
    }
}
