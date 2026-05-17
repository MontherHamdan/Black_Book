<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookDecoration;
use Illuminate\Support\Facades\Cache;

class BookDecorationController extends Controller
{
    public function index()
    {
        $bookDecorations = Cache::remember('book_decorations', 3600, function () {
            return BookDecoration::all();
        });

        return response()->json(['status' => 'success', 'data' => $bookDecorations]);
    }
}
