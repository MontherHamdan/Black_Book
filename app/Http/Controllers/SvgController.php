<?php

namespace App\Http\Controllers;

use App\Models\Svg;
use Illuminate\Http\Request;

class SvgController extends Controller
{
    public function index()
    {
        $svgs = Svg::all(); // Retrieve all SVGs
        return view('admin.svgViews.index', compact('svgs'));
    }

    public function create()
    {
        return view('admin.svgViews.create'); // Show form to create an SVG
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'svg_code' => 'required|string',
        ]);

        Svg::create($validated);

        return redirect()->route('svgs.index')->with('success', 'SVG created successfully.');
    }

    public function show(Svg $svg)
    {
        return view('svgs.show', compact('svg')); // Show details of a specific SVG
    }

    public function edit(Svg $svg)
    {
        return view('admin.svgViews.edit', compact('svg')); // Show form to edit an SVG
    }

    public function update(Request $request, Svg $svg)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'svg_code' => 'required|string',
        ]);

        $svg->update($validated);

        return redirect()->route('svgs.index')->with('success', 'SVG updated successfully.');
    }

    public function destroy(Svg $svg)
    {
        $svg->delete();

        return redirect()->route('svgs.index')->with('success', 'SVG deleted successfully.');
    }
}
