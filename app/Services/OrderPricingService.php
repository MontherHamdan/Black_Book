<?php

namespace App\Services;

use App\Models\BookType;
use App\Models\DiscountCode;

class OrderPricingService
{
    /**
     * Calculate the final price for an order without discount.
     *
     * @param array $orderData
     * @return float
     */
    public function calculateBasePrice(array $orderData): float
    {
        $price = 0;

        // 1. Get the base price from book type
        if (isset($orderData['book_type_id'])) {
            $bookType = BookType::find($orderData['book_type_id']);
            if ($bookType && $bookType->price) {
                $price += (float) $bookType->price;
            }
        }

        // 2. Add sponge cost
        if (isset($orderData['is_sponge']) && $orderData['is_sponge']) {
            $price += config('pricing.sponge_cost', 0);
        }

        // 3. Calculate page costs (extra pages beyond base)
        if (isset($orderData['pages_number'])) {
            $pagesNumber = (int) $orderData['pages_number'];
            $basePageCount = config('pricing.page_base_count', 50);
            $costPerExtraPage = config('pricing.cost_per_extra_page', 0.5);
            
            if ($pagesNumber > $basePageCount) {
                $extraPages = $pagesNumber - $basePageCount;
                $price += $extraPages * $costPerExtraPage;
            }
        }

        // 4. Add back images cost
        if (isset($orderData['back_image_ids']) && is_array($orderData['back_image_ids'])) {
            $backImageCount = count($orderData['back_image_ids']);
            $price += $backImageCount * config('pricing.back_image_cost_per_image', 0);
        }

        // 5. Add transparent printing images cost
        if (isset($orderData['transparent_printing_ids']) && is_array($orderData['transparent_printing_ids'])) {
            $transparentPrintingCount = count($orderData['transparent_printing_ids']);
            $price += $transparentPrintingCount * config('pricing.transparent_printing_cost_per_image', 0);
        }

        // 6. Add additional images cost
        if (isset($orderData['additional_images']) && is_array($orderData['additional_images'])) {
            $additionalImageCount = count($orderData['additional_images']);
            $price += $additionalImageCount * config('pricing.additional_image_cost_per_image', 0);
        }

        // 7. Add gift cost
        if (isset($orderData['gift_type'])) {
            if ($orderData['gift_type'] === 'custom') {
                $price += config('pricing.gift_custom_cost', 0);
            } elseif ($orderData['gift_type'] === 'default') {
                $price += config('pricing.gift_default_cost', 0);
            }
        }

        // 8. Add additives cost
        if (isset($orderData['is_with_additives']) && $orderData['is_with_additives']) {
            $price += config('pricing.with_additives_cost', 0);
        }

        // 9. Add decoration cost
        if (isset($orderData['book_decorations_id']) && !empty($orderData['book_decorations_id'])) {
            $price += config('pricing.decoration_cost', 0);
        }

        return round($price, 2);
    }

    /**
     * Calculate the final price with discount applied.
     *
     * @param float $basePrice
     * @param int|null $discountCodeId
     * @return float
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
            // Percentage discount
            $discountAmount = ($basePrice * $discountCode->discount_value) / 100;
        } elseif ($discountCode->discount_type === 'byJd') {
            // Fixed amount discount
            $discountAmount = $discountCode->discount_value;
        }

        $finalPrice = $basePrice - $discountAmount;
        
        // Ensure price doesn't go below zero
        $finalPrice = max(0, $finalPrice);

        return round($finalPrice, 2);
    }

    /**
     * Calculate both base price and price with discount.
     *
     * @param array $orderData
     * @return array ['base_price' => float, 'price_with_discount' => float]
     */
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

    /**
     * Validate that frontend-provided prices match server-side calculations.
     *
     * @param array $orderData
     * @param float|null $frontendBasePrice
     * @param float|null $frontendDiscountedPrice
     * @return array ['is_valid' => bool, 'errors' => array, 'calculated_prices' => array]
     */
    public function validateFrontendPrices(
        array $orderData,
        ?float $frontendBasePrice = null,
        ?float $frontendDiscountedPrice = null
    ): array {
        $calculatedPrices = $this->calculateOrderPrices($orderData);
        $errors = [];
        $isValid = true;

        // Allow small rounding differences (0.01)
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

