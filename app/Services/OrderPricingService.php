<?php

namespace App\Services;

use App\Models\BookType;
use App\Models\DiscountCode;

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

        // 1. سعر المنتج الأساسي
        if (!empty($orderData['book_type_id'])) {
            $bookType = BookType::find($orderData['book_type_id']);
            if ($bookType && $bookType->price) {
                $price += (float) $bookType->price;
            }
        }

        // 2. الزخرفة (2.5 دينار)
        if (!empty($orderData['book_decorations_id'])) {
            $price += config('pricing.decoration_cost', 2.5);
        }

        // 3. الصور الخلفية (1 دينار للصورة)
        $backCount = $this->countJsonItems($orderData, 'back_image_ids');
        if ($backCount > 0) {
            $price += $backCount * config('pricing.back_image_cost_per_image', 1);
        }

        // 4. الصور الإضافية (1 دينار للصورة)
        $additionalCount = $this->countJsonItems($orderData, 'additional_image_id');
        if ($additionalCount > 0) {
            $price += $additionalCount * config('pricing.additional_image_cost_per_image', 1);
        }

        // 5. الإهداء (Custom = 3 دنانير، والباقي مجاني)
        if (!empty($orderData['gift_type'])) {
            if ($orderData['gift_type'] === 'custom') {
                $price += config('pricing.gift_custom_cost', 3);
            }
        }

        // ملاحظة: تم إزالة كود (الإسفنج، الصفحات، الطباعة الشفافة) لأن تكلفتهم 0 أو ملغاة.

        return round($price, 2);
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
     */
    public function calculatePriceWithDiscount(float $basePrice, ?int $discountCodeId): float
    {
        if (!$discountCodeId) {
            return $basePrice;
        }

        $discountCode = DiscountCode::find($discountCodeId);
        
        if (!$discountCode) {
            return $basePrice;
        }

        $discountAmount = 0;

        if ($discountCode->discount_type === 'percentage') {
            $discountAmount = ($basePrice * $discountCode->discount_value) / 100;
        } elseif ($discountCode->discount_type === 'byJd') {
            $discountAmount = $discountCode->discount_value;
        }

        $finalPrice = $basePrice - $discountAmount;
        $finalPrice = max(0, $finalPrice);

        return round($finalPrice, 2);
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