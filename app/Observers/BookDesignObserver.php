<?php

namespace App\Observers;

use App\Models\BookDesign;
use Illuminate\Support\Facades\Cache;

class BookDesignObserver
{
    // Incrementing the version orphans all paginated cache combos instantly
    public function saved(BookDesign $bookDesign): void
    {
        Cache::increment('book_designs_version');
    }

    public function deleted(BookDesign $bookDesign): void
    {
        Cache::increment('book_designs_version');
    }
}
