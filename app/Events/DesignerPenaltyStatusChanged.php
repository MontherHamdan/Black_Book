<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class DesignerPenaltyStatusChanged implements ShouldBroadcastNow
{
    public int $designerId;
    public string $designerName;
    public int $needsModCount;
    public bool $isPenalized;
    public ?string $penalizedUntil;

    public function __construct(int $designerId, string $designerName, int $needsModCount, bool $isPenalized, ?string $penalizedUntil)
    {
        $this->designerId = $designerId;
        $this->designerName = $designerName;
        $this->needsModCount = $needsModCount;
        $this->isPenalized = $isPenalized;
        $this->penalizedUntil = $penalizedUntil;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.Models.User.' . $this->designerId);
    }

    public function broadcastWith(): array
    {
        return [
            'designerId' => $this->designerId,
            'designerName' => $this->designerName,
            'needsModCount' => $this->needsModCount,
            'isPenalized' => $this->isPenalized,
            'penalizedUntil' => $this->penalizedUntil,
        ];
    }
}
