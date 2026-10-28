<?php

namespace App\Listeners;

use App\Events\QualityAlert;
use App\Services\NotificationService;

class NotifyQualityInspector
{
    public function __construct(protected NotificationService $notificationService) {}

    public function handle(QualityAlert $event): void
    {
        $this->notificationService->notifyQualityAlert(
            $event->inspection->product->name,
            $event->message
        );
    }
}
