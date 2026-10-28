<?php

namespace App\Listeners;

use App\Events\MachineBreakdownResolved;

class RestoreMachineStatus
{
    public function handle(MachineBreakdownResolved $event): void
    {
        $event->breakdown->machine->update(['status' => 'idle']);
    }
}
