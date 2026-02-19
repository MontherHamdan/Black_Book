<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanWebController extends Controller
{
    public function index()
    {
        $plans = Plan::orderByDesc('id')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'discount_price' => 'required|numeric|min:0',
            'book_price' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'person_number' => 'nullable|string|max:255',
        ]);

        Plan::create([
            'title' => $request->title,
            'discount_price' => $request->discount_price,
            'book_price' => $request->book_price,
            'features' => $request->features ? array_values(array_filter($request->features)) : [],
            'person_number' => $request->person_number,
        ]);

        return redirect()->route('plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'discount_price' => 'required|numeric|min:0',
            'book_price' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'person_number' => 'nullable|string|max:255',
        ]);

        $plan->update([
            'title' => $request->title,
            'discount_price' => $request->discount_price,
            'book_price' => $request->book_price,
            'features' => $request->features ? array_values(array_filter($request->features)) : [],
            'person_number' => $request->person_number,
        ]);

        return redirect()->route('plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'Plan deleted successfully.');
    }
}
