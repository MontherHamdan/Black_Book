<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Major;


class MajorController extends Controller
{
    public function index($universityId)
    {
        $majors = Major::where('university_id', $universityId)->get();

        if ($majors->isEmpty()) {
            return response()->json(['error' => 'No majors found for this university'], 404);
        }

        return response()->json($majors, 200);
    }
}
