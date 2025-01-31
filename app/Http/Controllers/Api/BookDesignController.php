<?php

namespace App\Http\Controllers\Api;

use App\Models\BookDesign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BookDesignResource;

class BookDesignController extends Controller
{
    /**
     * Build a query for fetching book designs.
     *
     * @param  Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildQuery(Request $request)
    {
        $query = BookDesign::where('is_uploaded_by_user', false);

        // Filter by category_id
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by sub_category_id
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->input('sub_category_id'));
        }

        // Include relationships
        return $query->with(['category', 'subcategory']);
    }

    /**
     * Fetch paginated book designs with filters and relationships.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        // Determine items per page (default is 10)
        $perPage = $request->input('per_page', 10);

        // Fetch paginated designs
        $designs = $query->paginate($perPage);

        // Return paginated response
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

    /**
     * Fetch all book designs with filters and relationships.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request)
    {
        $query = $this->buildQuery($request);

        // Fetch all designs without pagination
        $designs = $query->get();

        // Return the full collection
        return response()->json([
            'data' => BookDesignResource::collection($designs),
            'total' => $designs->count(), // Optional metadata
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,webp|max:1048',
        ]);

        // Store the image
        $imageFile = $request->file('image');
        $imageName = time() . '_' . $imageFile->getClientOriginalName();
        $imagePath = $imageFile->storeAs('book_designs', $imageName, 'public');
        $imageUrl = url('storage/' . $imagePath);
        
        // Save to database
        $bookDesign = BookDesign::create([
            'image' => $imageUrl,
            'is_uploaded_by_user' => true, // Always set to true for user uploads
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Book design uploaded successfully.',
            'data' => $bookDesign,
        ]);
    }

}
