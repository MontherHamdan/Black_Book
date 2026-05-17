<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookTypeResource;
use App\Models\BookType;
use Illuminate\Support\Facades\Cache;

class BookTypeController extends Controller
{
    public function index()
    {
        $bookTypes = Cache::remember('book_types', 3600, function () {
            return BookType::with('subMedia')->get();
        });

        return BookTypeResource::collection($bookTypes);
    }

    public function store() {}
    public function show() {}
    public function update() {}
    public function destroy() {}
}
