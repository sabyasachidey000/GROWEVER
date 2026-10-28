<?php

namespace App\Listeners;

use App\Events\MachineBreakdownResolved;
use App\Services\NotificationService;

class NotifyProductionManager
{
    public function __construct(protected NotificationService $notificationService) {}

    public function handle(MachineBreakdownResolved $event): void
    {
        $this->notificationService->sendToRole(
            'Production Manager',
            'maintenance_request',
            'Breakdown Resolved',
            "Machine {$event->breakdown->machine->name} breakdown has been resolved.",
            ['machine_id' => $event->breakdown->machine_id],
            'medium'
        );
    }
}
