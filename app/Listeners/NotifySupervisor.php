<?php

namespace App\Listeners;

use App\Events\ProductionTargetMissed;
use App\Services\NotificationService;

class NotifySupervisor
{
    public function __construct(protected NotificationService $notificationService) {}

    public function handle(ProductionTargetMissed $event): void
    {
        $this->notificationService->notifyProductionTarget(
            $event->productionRun->machine->name,
            null,
            $event->productionRun->weight
        );
    }
}
