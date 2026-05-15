<?php

namespace App\Http\Controllers;

use App\Models\SvgName;
use Illuminate\Http\Request;

class SvgNameController extends Controller
{
    public function index()
    {
        $svgNames = SvgName::orderBy('name')->paginate(20);
        $allNormalizedNames = SvgName::pluck('normalized_name');

        return view('admin.svg_names.index', compact('svgNames', 'allNormalizedNames'));
    }

    public function create()
    {
        return view('admin.svg_names.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'normalized_name' => ['required', 'string', 'max:255'],
            'svg_code' => ['nullable', 'string'], // longText
        ]);

        SvgName::create($data);

        return redirect()
            ->route('svg-names.index')
            ->with('success', 'SVG name and code added successfully.');
    }

    public function edit(SvgName $svgName)
    {
        return view('admin.svg_names.edit', compact('svgName'));
    }

    public function update(Request $request, SvgName $svgName)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'normalized_name' => ['required', 'string', 'max:255'],
            'svg_code' => ['nullable', 'string'],
        ]);

        $svgName->update($data);

        return redirect()
            ->route('svg-names.index')
            ->with('success', 'SVG name and code updated successfully.');
    }

    public function destroy(SvgName $svgName)
    {
        $svgName->delete();

        return redirect()
            ->route('svg-names.index')
            ->with('success', 'SVG name and code deleted successfully.');
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'svgs' => 'required|array|min:1|max:500',
            'svgs.*.name' => 'required|string|max:255',
            'svgs.*.code' => 'required|string',
        ]);

        $results = ['created' => 0, 'updated' => 0, 'failed' => []];

        foreach ($request->input('svgs') as $item) {
            try {
                $rawName = trim($item['name']);
                $normalized = \App\Support\ArabicNameNormalizer::normalize($rawName);

                if (empty($normalized)) {
                    $results['failed'][] = $rawName;

                    continue;
                }

                $existed = SvgName::where('normalized_name', $normalized)->exists();

                SvgName::updateOrCreate(
                    ['normalized_name' => $normalized],
                    ['name' => $rawName, 'svg_code' => $item['code']]
                );

                $existed ? $results['updated']++ : $results['created']++;

            } catch (\Throwable $e) {
                $results['failed'][] = $item['name'] ?? '?';
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }
}
