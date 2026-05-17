<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diploma;
use Illuminate\Support\Facades\Cache;

class DiplomaController extends Controller
{
    public function index()
    {
        $diplomas = Cache::remember('diplomas', 3600, function () {
            return Diploma::all();
        });

        return response()->json($diplomas, 200);
    }
}
