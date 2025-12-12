<?php

namespace App\Http\Controllers\Api;

use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

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

    /**
     * Store a newly created discount code.
     */
    public function store(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'group' => 'required|string|in:G1,G2,G3,G4,G5',
        ]);
    
        // Map group to discount values
        $discountValues = [
            'G1' => 1,
            'G2' => 2,
            'G3' => 3,
            'G4' => 4,
            'G5' => 5,
        ];
    
        // Generate a unique 5-character alphanumeric discount code
        do {
            $discountCode = Str::upper(Str::random(5));
        } while (DiscountCode::where('discount_code', $discountCode)->exists());
    
        // Create the discount code
        $discount = DiscountCode::create([
            'discount_code' => $discountCode,
            'discount_value' => $discountValues[$validated['group']],
            'discount_type' => 'byJd',

        ]);
    
        return response()->json([
            'success' => true,
            
            'message' => 'Discount code created successfully.',
            'data' => $discount,
        ], 201);
    }
}
