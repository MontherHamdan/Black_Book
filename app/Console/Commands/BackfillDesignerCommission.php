<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class BackfillDesignerCommission extends Command
{
    protected $signature = 'orders:backfill-commission';
    protected $description = 'Backfill designer_commission for all existing orders that have a designer and are in done statuses but still have NULL commission.';

    public function handle(): int
    {
        $doneStatuses = ['preparing', 'Completed', 'Received', 'Out for Delivery', 'Canceled'];

        $orders = Order::with('designer')
            ->whereNotNull('designer_id')
            ->whereIn('status', $doneStatuses)
            ->whereNull('designer_commission')
            ->get();

        $this->info("Found {$orders->count()} orders to backfill.");

        $updated = 0;

        foreach ($orders as $order) {
            $designer = $order->designer;

            if (!$designer) {
                $this->warn("Order #{$order->id}: designer not found, skipping.");
                continue;
            }

            $commission = (float) ($designer->base_order_price ?? 0);

            if (!empty($order->book_decorations_id)) {
                $commission += (float) ($designer->decoration_price ?? 0);
            }

            if ($order->gift_type === 'custom') {
                $commission += (float) ($designer->custom_gift_price ?? 0);
            }

            $additionalIds = $order->additional_image_id;
            if (is_string($additionalIds)) {
                $additionalIds = json_decode($additionalIds, true);
            }
            if (is_array($additionalIds) && !empty($additionalIds)) {
                $commission += (float) ($designer->internal_image_price ?? 0);
            }

            $order->designer_commission = $commission;

            if (!$order->designer_done) {
                $order->designer_done = true;
            }
            if (!$order->designer_done_at) {
                $order->designer_done_at = now();
            }

            $order->save();
            $updated++;

            $this->line("  Order #{$order->id} → commission = {$commission}");
        }

        $this->info("Done! Updated {$updated} orders.");

        return self::SUCCESS;
    }
}
