<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use App\Models\DiplomaMajor;
use Illuminate\Http\Request;

class DiplomaController extends Controller
{
    public function index()
    {
        // Fetch all diplomas along with the count of majors associated with each
        $diplomas = Diploma::withCount('majors')->get();
        return view('admin.diplomas.index', compact('diplomas'));
    }

    public function create()
    {
        return view('admin.diplomas.create');
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_name' => 'required|string|max:255',  // Add validation for governorate_name
        ]);

        // Create a new diploma
        Diploma::create([
            'name' => $request->name,
            'governorate_name' => $request->governorate_name,  // Ensure governorate_name is saved
        ]);

        return redirect()->route('diplomas.index')->with('success', 'Diploma added successfully.');
    }


    public function edit(Diploma $diploma)
    {
        return view('admin.diplomas.edit', compact('diploma'));
    }

    public function update(Request $request, Diploma $diploma)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_name' => 'required|string|max:255',  // Add validation for governorate_name
        ]);

        // Update the diploma with new values
        $diploma->update([
            'name' => $request->name,
            'governorate_name' => $request->governorate_name,  // Ensure governorate_name is updated
        ]);

        return redirect()->route('diplomas.index')->with('success', 'Diploma updated successfully.');
    }


    public function destroy(Diploma $diploma)
    {
        // Delete the diploma
        $diploma->delete();

        return redirect()->route('diplomas.index')->with('success', 'Diploma deleted successfully.');
    }

    public function storeMajor(Request $request, $diplomaId)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new diploma major for the given diploma
        DiplomaMajor::create([
            'name' => $request->name,
            'diploma_id' => $diplomaId,
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteMajor($diplomaId, $majorId)
    {
        // Find the major by diploma_id and major_id
        $major = DiplomaMajor::where('diploma_id', $diplomaId)->findOrFail($majorId);

        // Delete the major
        $major->delete();

        // Return the updated partial
        return redirect()->route('diplomas.index')->with('success', 'Major deleted successfully.');
    }

    public function fetchMajors($diplomaId)
    {
        // Fetch all majors for a given diploma
        $majors = DiplomaMajor::where('diploma_id', $diplomaId)->get();

        // Return the rendered Blade partial for the majors list
        return view('admin.diplomas.partials.majors_list', compact('majors'))->render();
    }
}
