<?php

namespace App\Events;

use App\Models\ProductionApproval;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(public ProductionApproval $approval) {}
}
