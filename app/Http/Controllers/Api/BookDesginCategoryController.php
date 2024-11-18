<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookDesign;
use App\Models\BookDesignCategory;
use Illuminate\Http\Request;

class BookDesginCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BookDesignCategory::get();
    }
}
