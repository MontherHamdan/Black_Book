<?php

namespace App\Observers;

use App\Models\BookType;
use Illuminate\Support\Facades\Cache;

class BookTypeObserver
{
    public function saved(BookType $bookType): void
    {
        Cache::forget('book_types');
    }

    public function deleted(BookType $bookType): void
    {
        Cache::forget('book_types');
    }
}
