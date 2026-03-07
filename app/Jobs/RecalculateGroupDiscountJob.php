<?php

namespace App\Jobs;

use App\Models\DiscountCodeTier;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateGroupDiscountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $discountCodeId;
    public float $discountValue;
    public string $discountType;

    public function __construct(int $discountCodeId, DiscountCodeTier $tier)
    {
        $this->discountCodeId = $discountCodeId;
        $this->discountValue = (float) $tier->discount_value;
        $this->discountType = $tier->discount_type;
    }

    public function handle(): void
    {
        Order::where('discount_code_id', $this->discountCodeId)
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $basePrice = (float) $order->final_price;

                    if ($basePrice <= 0) {
                        continue;
                    }

                    $discountAmount = 0;
                    if ($this->discountType === 'percentage') {
                        $discountAmount = ($basePrice * $this->discountValue) / 100;
                    } elseif ($this->discountType === 'byJd') {
                        $discountAmount = $this->discountValue;
                    }

                    $newPrice = max(0, round($basePrice - $discountAmount, 2));
                    $order->final_price_with_discount = $newPrice;
                    $order->saveQuietly();
                }
            });
    }
}
