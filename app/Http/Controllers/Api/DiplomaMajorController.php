<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiplomaMajor;
use Illuminate\Support\Facades\Cache;

class DiplomaMajorController extends Controller
{
    public function index(int $diplomaId)
    {
        $majors = Cache::remember("diploma_majors_{$diplomaId}", 3600, function () use ($diplomaId) {
            return DiplomaMajor::where('diploma_id', $diplomaId)->get();
        });

        if ($majors->isEmpty()) {
            return response()->json(['error' => 'No majors found for this college'], 404);
        }

        return response()->json($majors, 200);
    }
}
