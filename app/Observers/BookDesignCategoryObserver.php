<?php

namespace App\Observers;

use App\Models\BookDesignCategory;
use Illuminate\Support\Facades\Cache;

class BookDesignCategoryObserver
{
    public function saved(BookDesignCategory $_category): void
    {
        Cache::forget('book_design_categories');
    }

    public function deleted(BookDesignCategory $_category): void
    {
        Cache::forget('book_design_categories');
    }
}
