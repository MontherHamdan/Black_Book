<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use App\Models\DiplomaMajor;
use Illuminate\Http\Request;

class DiplomaController extends Controller
{
    public function index()
    {
        // Fetch all diplomas along with location relations and the count of majors
        $diplomas = Diploma::with(['governorate', 'city', 'area'])->withCount('majors')->get();
        return view('admin.diplomas.index', compact('diplomas'));
    }

    public function create()
    {
        $governorates = \App\Models\Governorate::where('is_active', true)->get();
        return view('admin.diplomas.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $data = $request->only(['name', 'governorate_name', 'governorate_id', 'city_id', 'area_id']);
        if ($request->filled('governorate_id') && empty($data['governorate_name'])) {
            $gov = \App\Models\Governorate::find($request->governorate_id);
            $data['governorate_name'] = $gov?->name_ar ?? null;
        }

        // Create a new diploma
        Diploma::create($data);

        return redirect()->route('diplomas.index')->with('success', 'Diploma added successfully.');
    }

    public function edit(Diploma $diploma)
    {
        $governorates = \App\Models\Governorate::where('is_active', true)->get();
        $cities = $diploma->governorate_id
            ? \App\Models\City::where('governorate_id', $diploma->governorate_id)->get()
            : collect();
        $areas = $diploma->city_id
            ? \App\Models\Area::where('city_id', $diploma->city_id)->get()
            : collect();

        return view('admin.diplomas.edit', compact('diploma', 'governorates', 'cities', 'areas'));
    }

    public function update(Request $request, Diploma $diploma)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $data = $request->only(['name', 'governorate_name', 'governorate_id', 'city_id', 'area_id']);
        if ($request->filled('governorate_id') && empty($data['governorate_name'])) {
            $gov = \App\Models\Governorate::find($request->governorate_id);
            $data['governorate_name'] = $gov?->name_ar ?? null;
        }

        // Update the diploma with new values
        $diploma->update($data);

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
