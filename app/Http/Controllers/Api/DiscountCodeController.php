<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;

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

        $discount = DiscountCode::with(['plan', 'university', 'governorate', 'city', 'area'])
            ->where('discount_code', strtoupper($request->code))
            ->first();

        if (! $discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount code not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Discount code is valid.',
            'id' => $discount->id,
            'discount_code' => $discount->discount_code,
            'discount_value' => $discount->is_group && $discount->plan_id ? (float) $discount->plan->discount_price : $discount->discount_value,
            'discount_type' => $discount->is_group ? 'byJd' : $discount->discount_type,
            'code_name' => $discount->code_name,
            'is_group' => (bool) $discount->is_group,
            'user_phone_number' => $discount->user_phone_number,
            'delivery_number_two' => $discount->delivery_number_two,
            'delivery_target' => $discount->university_id ? 'university' : ($discount->governorate_id ? 'home' : null),
            'university_id' => $discount->university_id,
            'governorate_id' => $discount->governorate_id,
            'city_id' => $discount->city_id,
            'area_id' => $discount->area_id,
        ]);
    }
}
