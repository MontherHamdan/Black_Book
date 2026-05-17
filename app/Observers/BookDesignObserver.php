<?php

namespace App\Observers;

use App\Models\BookDesign;
use Illuminate\Support\Facades\Cache;

class BookDesignObserver
{
    public function saved(BookDesign $_bookDesign): void
    {
        Cache::increment('book_designs_version');
    }

    public function deleted(BookDesign $_bookDesign): void
    {
        Cache::increment('book_designs_version');
    }

    public function restored(BookDesign $_bookDesign): void
    {
        Cache::increment('book_designs_version');
    }
}
