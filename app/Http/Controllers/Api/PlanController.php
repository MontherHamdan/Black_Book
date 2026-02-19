<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return response()->json(['success' => true, 'data' => $plans], 200);
    }

    public function store(Request $request)
    {
        if (Plan::count() >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot create more than 4 plans. The maximum limit is reached.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'discount_price' => 'required|numeric|min:0',
            'book_price' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'person_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $plan = Plan::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Plan created successfully.', 'data' => $plan], 201);
    }

    public function show($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $plan], 200);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'discount_price' => 'sometimes|required|numeric|min:0',
            'book_price' => 'sometimes|required|numeric|min:0',
            'features' => 'nullable|array',
            'person_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $plan->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Plan updated successfully.', 'data' => $plan], 200);
    }

    public function destroy($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found.'], 404);
        }

        $plan->delete();

        return response()->json(['success' => true, 'message' => 'Plan deleted successfully.'], 200);
    }
}