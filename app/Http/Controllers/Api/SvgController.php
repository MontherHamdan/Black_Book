<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Svg;
use App\Models\SvgCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SvgController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $svgs = Svg::paginate($perPage);
        return response()->json($svgs);
    }

    public function getCategoriesWithSvgs()
    {
        $categories = Cache::remember('svg_categories', 3600, function () {
            return SvgCategory::with('svgs')->get();
        });

        return response()->json(['success' => true, 'data' => $categories]);
    }
}
