<?php

namespace App\Events;

use App\Models\ProductionRun;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionTargetMissed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductionRun $productionRun,
        public float $targetPercentage
    ) {}
}
