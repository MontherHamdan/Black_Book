<?php

namespace App\Http\Controllers\Api;

use App\Models\BookDesign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookDesignResource;

class BookDesignController extends Controller
{
    public function index(Request $request)
    {
        $query = BookDesign::query();

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->input('category'));
            });
        }

        // Filter by subcategory
        if ($request->has('subcategory')) {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('name', $request->input('subcategory'));
            });
        }

        // Fetch filtered designs with relationships
        $designs = $query->with(['category', 'subcategory'])->get();

        return BookDesignResource::collection($designs);
    }
}
