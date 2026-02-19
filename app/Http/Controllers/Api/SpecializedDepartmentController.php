<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpecializedDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecializedDepartmentController extends Controller
{
    public function index()
    {
        $departments = SpecializedDepartment::latest()->get();
        return response()->json(['success' => true, 'data' => $departments], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'whatsapp_link' => 'nullable|url',
            'icon_svg' => 'nullable|string',
            'color_code' => 'nullable|string|max:20', // مثل: #ff5733
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $department = SpecializedDepartment::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Department created successfully.', 'data' => $department], 201);
    }

    public function show($id)
    {
        $department = SpecializedDepartment::find($id);

        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $department], 200);
    }

    public function update(Request $request, $id)
    {
        $department = SpecializedDepartment::find($id);

        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'whatsapp_link' => 'nullable|url',
            'icon_svg' => 'nullable|string',
            'color_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $department->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Department updated successfully.', 'data' => $department], 200);
    }

    public function destroy($id)
    {
        $department = SpecializedDepartment::find($id);

        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
        }

        $department->delete();

        return response()->json(['success' => true, 'message' => 'Department deleted successfully.'], 200);
    }
}