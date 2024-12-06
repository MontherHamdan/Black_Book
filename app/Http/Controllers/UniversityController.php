<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\Major;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::withCount('majors')->get();
        return view('admin.universities.index', compact('universities'));
    }

    public function create()
    {
        return view('admin.universities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_name' => 'nullable|string|max:255',
        ]);

        University::create($request->all());
        return redirect()->route('universities.index')->with('success', 'University added successfully.');
    }

    public function edit(University $university)
    {
        return view('admin.universities.edit', compact('university'));
    }

    public function update(Request $request, University $university)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_name' => 'nullable|string|max:255',
        ]);

        $university->update($request->all());
        return redirect()->route('universities.index')->with('success', 'University updated successfully.');
    }

    public function destroy(University $university)
    {
        $university->delete();
        return redirect()->route('universities.index')->with('success', 'University deleted successfully.');
    }

    public function storeMajor(Request $request, $universityId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Major::create([
            'name' => $request->name,
            'university_id' => $universityId,
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteMajor($universityId, $majorId)
    {
        $major = Major::where('university_id', $universityId)->findOrFail($majorId);

        $major->delete();

        // Return the updated partial
        return redirect()->route('universities.index')->with('success', 'Address deleted successfully.');
    }


    public function fetchMajors($universityId)
    {
        $majors = Major::where('university_id', $universityId)->get();

        // Return the rendered Blade partial
        return view('admin.universities.partials.majors_list', compact('majors'))->render();
    }
}
