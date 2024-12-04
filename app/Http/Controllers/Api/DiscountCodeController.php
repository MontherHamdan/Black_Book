<?php

namespace App\Http\Controllers\Api;

use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountCodeController extends Controller
{
    /**
     * Display a listing of the discount codes.
     */
    public function index()
    {
        $discountCodes = DiscountCode::all();
        return response()->json([
            'success' => true,
            'data' => $discountCodes,
        ]);
    }
}
