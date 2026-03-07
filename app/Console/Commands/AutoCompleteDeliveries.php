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

        $count = Order::where('status', 'out_for_delivery')
            ->where('updated_at', '<=', $cutoff)
            ->update(['status' => 'Received']);

        $this->info("Auto-completed {$count} deliveries to 'Received'.");

        return self::SUCCESS;
    }
}
