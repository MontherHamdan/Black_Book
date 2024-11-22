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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
