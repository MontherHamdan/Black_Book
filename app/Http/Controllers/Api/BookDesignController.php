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

        // Fetch filtered designs with relationships
        $designs = $query->with(['category', 'subcategory'])->get();

        return BookDesignResource::collection($designs);
    }
}
