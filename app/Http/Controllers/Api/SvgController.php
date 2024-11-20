<?php

namespace App\Http\Controllers\Api;

use App\Models\Svg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SvgController extends Controller
{

    /**
     * Display a paginated list of SVGs.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); 
        $svgs = Svg::paginate($perPage);

        return response()->json($svgs);
    }
}
