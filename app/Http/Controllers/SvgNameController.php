<?php

namespace App\Http\Controllers;

use App\Models\SvgName;
use Illuminate\Http\Request;

class SvgNameController extends Controller
{
    public function index()
    {
        $svgNames = SvgName::orderBy('name')->paginate(20);

        return view('admin.svg_names.index', compact('svgNames'));
    }

    public function create()
    {
        return view('admin.svg_names.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'normalized_name' => ['required', 'string', 'max:255'],
            'svg_code'        => ['nullable', 'string'], // longText
        ]);

        SvgName::create($data);

        return redirect()
            ->route('svg-names.index')
            ->with('success', 'تم إضافة الاسم و كود الـ SVG بنجاح.');
    }

    public function edit(SvgName $svgName)
    {
        return view('admin.svg_names.edit', compact('svgName'));
    }

    public function update(Request $request, SvgName $svgName)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'normalized_name' => ['required', 'string', 'max:255'],
            'svg_code'        => ['nullable', 'string'],
        ]);

        $svgName->update($data);

        return redirect()
            ->route('svg-names.index')
            ->with('success', 'تم تحديث بيانات الاسم و كود الـ SVG بنجاح.');
    }
}
