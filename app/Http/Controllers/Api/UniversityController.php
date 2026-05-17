<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Support\Facades\Cache;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = Cache::remember('universities', 3600, function () {
            return University::all();
        });

        return response()->json($universities, 200);
    }
}
