<?php

namespace App\Http\Controllers;

use App\Models\SpecializedDepartment;
use Illuminate\Http\Request;

class SpecializedDepartmentWebController extends Controller
{
    public function index()
    {
        $departments = SpecializedDepartment::orderByDesc('id')->get();
        return view('admin.specialized-departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.specialized-departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'whatsapp_link' => 'nullable|string|max:255',
            'icon_svg' => 'nullable|string',
            'color_code' => 'nullable|string|max:20',
        ]);

        SpecializedDepartment::create($request->all());

        return redirect()->route('specialized-departments.index')->with('success', 'Specialized Department created successfully.');
    }

    public function edit(SpecializedDepartment $specializedDepartment)
    {
        return view('admin.specialized-departments.edit', compact('specializedDepartment'));
    }

    public function update(Request $request, SpecializedDepartment $specializedDepartment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'whatsapp_link' => 'nullable|string|max:255',
            'icon_svg' => 'nullable|string',
            'color_code' => 'nullable|string|max:20',
        ]);

        $specializedDepartment->update($request->all());

        return redirect()->route('specialized-departments.index')->with('success', 'Specialized Department updated successfully.');
    }

    public function destroy(SpecializedDepartment $specializedDepartment)
    {
        $specializedDepartment->delete();
        return redirect()->route('specialized-departments.index')->with('success', 'Specialized Department deleted successfully.');
    }
}
