<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class PenaltyThresholdUpdated implements ShouldBroadcastNow
{
    public int $newThreshold;

    public function __construct(int $newThreshold)
    {
        $this->newThreshold = $newThreshold;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('admin-settings');
    }

    public function broadcastWith(): array
    {
        return [
            'newThreshold' => $this->newThreshold,
        ];
    }
}
