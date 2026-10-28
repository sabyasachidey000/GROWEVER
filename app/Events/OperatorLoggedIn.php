<?php

namespace App\Events;

use App\Models\OperatorSession;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OperatorLoggedIn
{
    use Dispatchable, SerializesModels;

    public function __construct(public OperatorSession $session) {}
}
