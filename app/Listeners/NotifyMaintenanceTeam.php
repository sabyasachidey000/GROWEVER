<?php

namespace App\Listeners;

use App\Events\MachineBreakdownReported;
use App\Services\NotificationService;

class NotifyMaintenanceTeam
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(MachineBreakdownReported $event): void
    {
        $this->notificationService->notifyMachineBreakdown(
            $event->breakdown->machine_id,
            $event->breakdown->breakdown_type,
            $event->breakdown->machine->name
        );
    }
}
