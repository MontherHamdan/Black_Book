<?php

namespace App\Http\Controllers\Api;

use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountCodeController extends Controller
{
    /**
     * Check whether a discount code is valid and return its details.
     *
     * GET /api/v1/discount_codes/check?code=XXXXX
     */
    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $discount = DiscountCode::where('discount_code', strtoupper($request->code))->first();

        if (! $discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount code not found.',
            ], 404);
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Discount code is valid.',
            'discount_code'  => $discount->discount_code,
            'discount_value' => $discount->discount_value,
            'discount_type'  => $discount->discount_type,
            'code_name'      => $discount->code_name,
        ]);
    }
}
