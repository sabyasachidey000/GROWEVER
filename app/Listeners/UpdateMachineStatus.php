<?php

namespace App\Listeners;

use App\Events\MachineBreakdownReported;

class UpdateMachineStatus
{
    public function handle(MachineBreakdownReported $event): void
    {
        $event->breakdown->machine->update(['status' => 'breakdown']);
    }
}
