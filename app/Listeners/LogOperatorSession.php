<?php

namespace App\Listeners;

use App\Events\OperatorLoggedIn;
use App\Services\ActivityLogService;

class LogOperatorSession
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function handle(OperatorLoggedIn $event): void
    {
        $session = $event->session;
        $this->activityLogService->log(
            "Operator {$session->employee->name} logged in to {$session->machine->name} (Shift: {$session->shift->name})",
            $session,
            'operator_login',
            'rfid'
        );
    }
}
