<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\BookDecoration;
use App\Http\Controllers\Controller;

class BookDecorationController extends Controller
{
    public function index()
    {
        $bookDecorations = BookDecoration::all();

        return response()->json([
            'status' => 'success',
            'data' => $bookDecorations,
        ]);
    }
}
