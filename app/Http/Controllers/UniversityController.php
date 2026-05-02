<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::with(['governorate', 'city', 'area'])->get();

        return view('admin.universities.index', compact('universities'));
    }

    public function create()
    {
        $governorates = \App\Models\Governorate::where('is_active', true)->get();

        return view('admin.universities.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        University::create($request->only(['name', 'governorate_name', 'governorate_id', 'city_id', 'area_id']));

        return redirect()->route('universities.index')->with('success', 'University created successfully.');
    }

    public function edit(University $university)
    {
        $governorates = \App\Models\Governorate::where('is_active', true)->get();
        $cities = $university->governorate_id
            ? \App\Models\City::where('governorate_id', $university->governorate_id)->get()
            : collect();
        $areas = $university->city_id
            ? \App\Models\Area::where('city_id', $university->city_id)->get()
            : collect();

        return view('admin.universities.edit', compact('university', 'governorates', 'cities', 'areas'));
    }

    public function update(Request $request, University $university)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $university->update($request->only(['name', 'governorate_name', 'governorate_id', 'city_id', 'area_id']));

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
