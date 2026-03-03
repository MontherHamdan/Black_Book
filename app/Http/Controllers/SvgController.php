<?php

namespace App\Http\Controllers;

use App\Models\Svg;
use Illuminate\Http\Request;
use App\Models\SvgCategory;

class SvgController extends Controller
{
    public function index()
    {
        $svgs = Svg::with('category')->get();
        return view('admin.svgViews.index', compact('svgs'));
    }

    public function create()
    {
        $categories = SvgCategory::all();
        return view('admin.svgViews.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'svg_code' => 'required|string',
            'category_id' => 'required|exists:svg_categories,id',
        ]);

        Svg::create($validated);

        return redirect()->route('svgs.index')->with('success', 'تمت إضافة الـ SVG بنجاح.');
    }

    public function edit(Svg $svg)
    {
        $categories = SvgCategory::all();
        return view('admin.svgViews.edit', compact('svg', 'categories'));
    }

    public function update(Request $request, Svg $svg)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'svg_code' => 'required|string',
            'category_id' => 'required|exists:svg_categories,id',
        ]);

        $svg->update($validated);

        return redirect()->route('svgs.index')->with('success', 'تم تعديل الـ SVG بنجاح.');
    }

    public function destroy(Svg $svg)
    {
        $svg->delete();

        return redirect()->route('svgs.index')->with('success', 'SVG deleted successfully.');
    }

    public function categoryIndex()
    {
        $categories = SvgCategory::withCount('svgs')->orderBy('id', 'desc')->get();
        return view('admin.svg_categories.index', compact('categories'));
    }
    public function createCategory()
    {
        return view('admin.svg_categories.create');
    }
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:svg_categories,name',
        ]);

        SvgCategory::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'تم إضافة القسم بنجاح.');
    }
    public function editCategory(SvgCategory $svgCategory)
    {
        return view('admin.svg_categories.edit', compact('svgCategory'));
    }

    public function updateCategory(Request $request, SvgCategory $svgCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:svg_categories,name,' . $svgCategory->id,
        ]);

        $svgCategory->update([
            'name' => $request->name
        ]);

        return redirect()->route('svg-categories.index')->with('success', 'تم تعديل القسم بنجاح.');
    }
    public function destroyCategory(SvgCategory $svgCategory)
    {
        $svgCategory->delete();
        return back()->with('success', 'تم حذف القسم بنجاح.');
    }
}
