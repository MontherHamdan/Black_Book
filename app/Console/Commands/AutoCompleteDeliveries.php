<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class AutoCompleteDeliveries extends Command
{
    protected $signature = 'deliveries:auto-complete';
    protected $description = 'Automatically mark out_for_delivery orders as Received after 24 hours';

    public function handle(): int
    {
        $cutoff = now()->subHours(24);

        $count = Order::where('status', Order::STATUS_OUT_FOR_DELIVERY)
            ->whereNotNull('dispatched_at')
            ->where('dispatched_at', '<=', $cutoff)
            ->update(['status' => Order::STATUS_RECEIVED]);

        $this->info("Auto-completed {$count} deliveries to 'Received' (using dispatched_at).");

        return self::SUCCESS;
    }
}
