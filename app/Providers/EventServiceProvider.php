<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // MES Events
        \App\Events\MachineBreakdownReported::class => [
            \App\Listeners\NotifyMaintenanceTeam::class,
            \App\Listeners\UpdateMachineStatus::class,
        ],

        \App\Events\MachineBreakdownResolved::class => [
            \App\Listeners\NotifyProductionManager::class,
            \App\Listeners\RestoreMachineStatus::class,
        ],

        \App\Events\ProductionTargetMissed::class => [
            \App\Listeners\NotifySupervisor::class,
        ],

        \App\Events\ProductionApproved::class => [
            \App\Listeners\CalculateOEE::class,
        ],

        \App\Events\QualityAlert::class => [
            \App\Listeners\NotifyQualityInspector::class,
        ],

        \App\Events\OperatorLoggedIn::class => [
            \App\Listeners\LogOperatorSession::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
