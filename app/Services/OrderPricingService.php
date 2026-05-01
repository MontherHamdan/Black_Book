<?php

namespace App\Services;

use App\Models\BookType;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\Log;

class OrderPricingService
{
    /**
     * Calculate the final price based on Client Rules.
     *
     * @param array $orderData
     * @return float
     */
    public function calculateBasePrice(array $orderData): float
    {
        $price = 0.0;
        $breakdown = [];

        // 1. سعر المنتج الأساسي
        if (!empty($orderData['book_type_id'])) {
            $bookType = BookType::withTrashed()->find($orderData['book_type_id']);

            if (!$bookType || $bookType->trashed()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'book_type_id' => ['The selected book type is deleted or no longer available.']
                ]);
            }

            if ($bookType->price) {
                $bookPrice = (float) $bookType->price;
                $price += $bookPrice;
                $breakdown['book_type'] = ['id' => $bookType->id, 'name' => $bookType->name_ar, 'price' => $bookPrice];
            }
        }

        // 2. الزخرفة (2.5 دينار)
        if (!empty($orderData['book_decorations_id'])) {
            $decorationCost = config('pricing.decoration_cost', 2.5);
            $price += $decorationCost;
            $breakdown['decoration'] = ['id' => $orderData['book_decorations_id'], 'cost' => $decorationCost];
        }

        // 3. الصور الإضافية (1 دينار للصورة)
        $additionalCount = $this->countJsonItems($orderData, 'additional_image_id');
        if ($additionalCount > 0) {
            $additionalCost = $additionalCount * config('pricing.additional_image_cost_per_image', 1);
            $price += $additionalCost;
            $breakdown['additional_images'] = ['count' => $additionalCount, 'cost_each' => config('pricing.additional_image_cost_per_image', 1), 'total' => $additionalCost];
        }

        // 5. الإهداء (Custom = 3 دنانير، والباقي مجاني)
        if (!empty($orderData['gift_type'])) {
            if ($orderData['gift_type'] === 'custom') {
                $giftCost = config('pricing.gift_custom_cost', 3);
                $price += $giftCost;
                $breakdown['gift_custom'] = ['cost' => $giftCost];
            }
        }

        $final = round($price, 2);

        Log::info('[Pricing] Base price calculated', [
            'breakdown' => $breakdown,
            'total_base_price' => $final,
        ]);

        return $final;
    }

    /**
     * Helper to safely count items in a JSON column or Array
     */
    private function countJsonItems(array $data, string $key): int
    {
        if (!isset($data[$key])) {
            return 0;
        }

        $value = $data[$key];

        if (is_array($value)) {
            return count($value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return count($decoded);
            }
        }

        return 0;
    }

    /**
     * Calculate price with discount.
     * When $orderCount is provided, tiered discounts are checked first.
     */
    public function calculatePriceWithDiscount(float $basePrice, ?int $discountCodeId, ?int $orderCount = null): float
    {
        if (!$discountCodeId) {
            Log::info('[Pricing] No discount code — returning base price', ['base_price' => $basePrice]);
            return $basePrice;
        }

        $discountCode = DiscountCode::find($discountCodeId);

        if (!$discountCode) {
            Log::warning('[Pricing] Discount code not found', ['discount_code_id' => $discountCodeId]);
            return $basePrice;
        }

        $discountValue = (float) $discountCode->discount_value;
        $discountType = $discountCode->discount_type;
        $source = 'discount_code_default';

        // Check for tier-based discount if order count is provided
        if ($orderCount !== null && $orderCount >= 2) {
            $tier = \App\Models\DiscountCodeTier::where('discount_code_id', $discountCodeId)
                ->where('min_qty', '<=', $orderCount)
                ->orderByDesc('min_qty')
                ->first();

            if ($tier) {
                $discountValue = (float) $tier->discount_value;
                $discountType = $tier->discount_type;
                $source = 'tier';
                Log::info('[Pricing] Tier discount matched', [
                    'tier_id'        => $tier->id,
                    'min_qty'        => $tier->min_qty,
                    'discount_value' => $discountValue,
                    'discount_type'  => $discountType,
                    'order_count'    => $orderCount,
                ]);
            } else {
                Log::info('[Pricing] No tier matched — falling back to default discount', [
                    'discount_code_id' => $discountCodeId,
                    'order_count'      => $orderCount,
                    'discount_value'   => $discountValue,
                    'discount_type'    => $discountType,
                ]);
            }
        }

        $discountAmount = 0;

        if ($discountType === 'percentage') {
            $discountAmount = ($basePrice * $discountValue) / 100;
        } elseif ($discountType === 'byJd') {
            $discountAmount = $discountValue;
        }

        $finalPrice = max(0, $basePrice - $discountAmount);
        $finalPrice = round($finalPrice, 2);

        Log::info('[Pricing] Discount applied', [
            'discount_code_id' => $discountCodeId,
            'code'             => $discountCode->discount_code,
            'is_group'         => (bool) $discountCode->is_group,
            'source'           => $source,
            'discount_type'    => $discountType,
            'discount_value'   => $discountValue,
            'discount_amount'  => $discountAmount,
            'base_price'       => $basePrice,
            'final_price'      => $finalPrice,
        ]);

        return $finalPrice;
    }

    public function calculateOrderPrices(array $orderData): array
    {
        $basePrice = $this->calculateBasePrice($orderData);
        $discountCodeId = $orderData['discount_code_id'] ?? null;
        $priceWithDiscount = $this->calculatePriceWithDiscount($basePrice, $discountCodeId);

        return [
            'base_price' => $basePrice,
            'price_with_discount' => $priceWithDiscount,
        ];
    }

    public function validateFrontendPrices(
        array $orderData,
        ?float $frontendBasePrice = null,
        ?float $frontendDiscountedPrice = null
    ): array {
        $calculatedPrices = $this->calculateOrderPrices($orderData);
        $errors = [];
        $isValid = true;
        $tolerance = 0.01;

        if ($frontendBasePrice !== null) {
            $difference = abs($calculatedPrices['base_price'] - $frontendBasePrice);
            if ($difference > $tolerance) {
                $errors[] = "Base price mismatch. Expected: {$calculatedPrices['base_price']}, Received: {$frontendBasePrice}";
                $isValid = false;
            }
        }

        if ($frontendDiscountedPrice !== null) {
            $difference = abs($calculatedPrices['price_with_discount'] - $frontendDiscountedPrice);
            if ($difference > $tolerance) {
                $errors[] = "Discounted price mismatch. Expected: {$calculatedPrices['price_with_discount']}, Received: {$frontendDiscountedPrice}";
                $isValid = false;
            }
        }

        return [
            'is_valid' => $isValid,
            'errors' => $errors,
            'calculated_prices' => $calculatedPrices,
        ];
    }
}