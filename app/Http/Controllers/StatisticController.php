<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statistics = Statistic::orderBy('sort_order', 'asc')->get();

        return view('admin.statistics.index', compact('statistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.statistics.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:50',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Statistic::create($validated);

        return redirect()->route('statistics.index')->with('success', 'تمت إضافة الإحصائية بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Statistic $statistic)
    {
        return view('admin.statistics.edit', compact('statistic'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Statistic $statistic)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:50',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $statistic->update($validated);

        return redirect()->route('statistics.index')->with('success', 'تم تعديل الإحصائية بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Statistic $statistic)
    {
        $statistic->delete();

        return redirect()->route('statistics.index')->with('success', 'تم حذف الإحصائية بنجاح.');
    }
}
