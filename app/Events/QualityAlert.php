<?php

namespace App\Events;

use App\Models\QualityInspection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QualityAlert
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public QualityInspection $inspection,
        public string $message
    ) {}
}
