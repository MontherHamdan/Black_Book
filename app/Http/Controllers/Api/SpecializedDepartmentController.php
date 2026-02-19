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


    public function show($id)
    {
        $department = SpecializedDepartment::find($id);

        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $department], 200);
    }





}