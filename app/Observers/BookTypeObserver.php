<?php

namespace App\Observers;

use App\Models\BookType;
use Illuminate\Support\Facades\Cache;

class BookTypeObserver
{
    public function saved(BookType $_bookType): void
    {
        Cache::forget('book_types');
    }

    public function deleted(BookType $_bookType): void
    {
        Cache::forget('book_types');
    }

    public function restored(BookType $_bookType): void
    {
        Cache::forget('book_types');
    }
}
