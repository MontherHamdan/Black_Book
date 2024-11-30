<?php

namespace App\Http\Controllers\Api;

use App\Models\BookDesign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BookDesignResource;

class BookDesignController extends Controller
{
    public function index(Request $request)
    {
        $query = BookDesign::query();

        // Filter by category_id
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by sub_category_id
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->input('sub_category_id'));
        }

        // Determine items per page (default is 10)
        $perPage = $request->input('per_page', 10); // Default to 10 items per page

        // Fetch paginated designs with relationships
        $designs = $query->with(['category', 'subcategory'])->paginate($perPage);

        // Return paginated response with resources
        return response()->json([
            'data' => BookDesignResource::collection($designs->items()), // Items for the current page
            'pagination' => [
                'total' => $designs->total(),
                'count' => $designs->count(),
                'per_page' => $designs->perPage(),
                'current_page' => $designs->currentPage(),
                'total_pages' => $designs->lastPage(),
            ],
        ]);
    }
}
