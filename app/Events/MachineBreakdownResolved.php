<?php

namespace App\Events;

use App\Models\MachineBreakdown;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MachineBreakdownResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public MachineBreakdown $breakdown) {}

    public function broadcastOn(): array
    {
        return [new Channel('breakdowns')];
    }
}
