<?php

namespace App\Http\Controllers\Api;

use App\Models\Diploma;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DiplomaMajor;

class DiplomaMajorController extends Controller
{
    public function index($diplomaId)
    {
        $majors = DiplomaMajor::where('diploma_id', $diplomaId)->get();

        if ($majors->isEmpty()) {
            return response()->json(['error' => 'No majors found for this college'], 404);
        }

        return response()->json($majors, 200);
    }
}
