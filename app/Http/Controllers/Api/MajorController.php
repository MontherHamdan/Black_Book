<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Support\Facades\Cache;

class MajorController extends Controller
{
    public function index(int $universityId)
    {
        $majors = Cache::remember("majors_{$universityId}", 3600, function () use ($universityId) {
            return Major::where('university_id', $universityId)->get();
        });

        if ($majors->isEmpty()) {
            return response()->json(['error' => 'No majors found for this university'], 404);
        }

        return response()->json($majors, 200);
    }
}
