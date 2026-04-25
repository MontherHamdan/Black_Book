<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Plan;
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

        $discount = DiscountCode::where('discount_code', strtoupper($request->code))->first();

        if (! $discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount code not found.',
            ], 404);
        }

        // ─── خصم فردي: ارجع القيمة كما هي ───────────────────────────────
        if (! $discount->is_group || ! $discount->plan_id) {
            return response()->json([
                'id' => $discount->id,
                'success' => true,
                'message' => 'Discount code is valid.',
                'discount_code' => $discount->discount_code,
                'discount_value' => (float) $discount->discount_value,
                'discount_type' => $discount->discount_type,
                'code_name' => $discount->code_name,
                'is_group' => false,
            ]);
        }

        // ─── خصم مجموعات: احسب القيمة بناءً على عدد المستخدمين الحاليين ──
        // 1. عدد الطلبات الحالية التي تستخدم هذا الكود + 1 (هذا الطلب الجديد)
        $currentUsageCount = Order::where('discount_code_id', $discount->id)->count() + 1;

        // 2. أفضل خطة تنطبق: أعلى خطة person_number ≤ العدد الحالي
        $matchedPlan = Plan::where('person_number', '<=', $currentUsageCount)
            ->orderByDesc('person_number')
            ->first();

        // 3. الخطة المربوطة بالكود (الخطة المستهدفة)
        $targetPlan = Plan::find($discount->plan_id);

        if ($matchedPlan) {
            // وصلوا لخطة → ارجع خصمها
            $discountValue = (float) $matchedPlan->discount_price;
            $isEligible = true;
            $planTitle = $matchedPlan->title ?? ('Plan '.$matchedPlan->id);
        } else {
            // لم يصلوا لأقل خطة بعد → لا يستحقون خصمًا بعد
            $discountValue = 0;
            $isEligible = false;
            $planTitle = $targetPlan?->title ?? null;
        }

        return response()->json([
            'id' => $discount->id,
            'success' => true,
            'message' => 'Discount code is valid.',
            'discount_code' => $discount->discount_code,
            'discount_value' => $discountValue,
            'discount_type' => $discount->discount_type,
            'code_name' => $discount->code_name,
            'is_group' => true,
            'is_eligible' => $isEligible,
            'current_count' => $currentUsageCount,
            'required_count' => (int) ($targetPlan?->person_number ?? 0),
            'applied_plan' => $planTitle,
        ]);
    }
}
