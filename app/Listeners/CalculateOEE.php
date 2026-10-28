<?php

namespace App\Listeners;

use App\Events\ProductionApproved;
use App\Services\OeeService;

class CalculateOEE
{
    public function __construct(protected OeeService $oeeService) {}

    public function handle(ProductionApproved $event): void
    {
        $approval = $event->approval;
        $run = $approval->productionRun;

        $this->oeeService->calculateOee(
            $run->machine_id,
            $run->start_time->toDateString(),
            $run->shift_id
        );
    }
}
