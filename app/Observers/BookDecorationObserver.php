<?php

namespace App\Observers;

use App\Models\BookDecoration;
use Illuminate\Support\Facades\Cache;

class BookDecorationObserver
{
    public function saved(BookDecoration $bookDecoration): void
    {
        Cache::forget('book_decorations');
    }

    public function deleted(BookDecoration $bookDecoration): void
    {
        Cache::forget('book_decorations');
    }
}
