<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BookDesignSubCategory;

class BookDesginSubCategoryController extends Controller
{
    /**
     * Display a listing of the sub categoris based on category id.
     */
    public function index(Request $request)
    {
        // Validate the category_id input
        $validated = $request->validate([
            'category_id' => 'required|exists:book_design_categories,id',
        ]);

        // Fetch subcategories by category_id
        $subcategories = BookDesignSubCategory::where('category_id', $validated['category_id'])->get();

        // Check if subcategories are found
        if ($subcategories->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No subcategories found for the given category.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategories fetched successfully.',
            'data' => $subcategories,
        ], 200);
    }
}
