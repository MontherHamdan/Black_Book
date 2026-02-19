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



    public function show($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $plan], 200);
    }





}