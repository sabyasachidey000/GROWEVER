<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendInAppNotification(int $userId, string $type, string $title, string $message, array $data = [], string $priority = 'medium'): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'channel' => 'in_app',
            'priority' => $priority,
        ]);
    }

    public function sendToRole(string $role, string $type, string $title, string $message, array $data = [], string $priority = 'medium'): void
    {
        $users = User::role($role)->active()->get();

        foreach ($users as $user) {
            $this->sendInAppNotification($user->id, $type, $title, $message, $data, $priority);
        }
    }

    public function notifyMachineBreakdown(int $machineId, string $breakdownType, string $machineName): void
    {
        $message = "Machine {$machineName} has a {$breakdownType} breakdown.";

        // Notify role-specific managers
        $this->sendToRole(
            'Maintenance Manager',
            'machine_breakdown',
            'Machine Breakdown Alert',
            $message,
            ['machine_id' => $machineId, 'breakdown_type' => $breakdownType],
            'high'
        );

        $this->sendToRole(
            'Production Manager',
            'machine_breakdown',
            'Machine Breakdown Alert',
            $message,
            ['machine_id' => $machineId, 'breakdown_type' => $breakdownType],
            'high'
        );

        // Fallback: notify all Super Admins if no role-specific users exist
        $this->sendToRole(
            'Super Admin',
            'machine_breakdown',
            'Machine Breakdown Alert',
            $message,
            ['machine_id' => $machineId, 'breakdown_type' => $breakdownType],
            'high'
        );

        // Also notify Shift Supervisors as a safety net
        $this->sendToRole(
            'Shift Supervisor',
            'machine_breakdown',
            'Machine Breakdown Alert',
            $message,
            ['machine_id' => $machineId, 'breakdown_type' => $breakdownType],
            'high'
        );
    }

    public function notifyProductionTarget($machineName, $target, $actual): void
    {
        $message = "Machine {$machineName} produced {$actual} against target of {$target}.";

        $this->sendToRole(
            'Production Manager',
            'production_target',
            'Production Target Alert',
            $message,
            ['machine_name' => $machineName, 'target' => $target, 'actual' => $actual],
            'medium'
        );
    }

    public function notifyApprovalPending(int $approvalId, string $submitterName): void
    {
        $message = "Production data submitted by {$submitterName} is pending approval.";

        $this->sendToRole(
            'Shift Supervisor',
            'approval_pending',
            'Approval Pending',
            $message,
            ['approval_id' => $approvalId, 'submitter' => $submitterName],
            'medium'
        );

        $this->sendToRole(
            'Production Manager',
            'approval_pending',
            'Approval Pending',
            $message,
            ['approval_id' => $approvalId, 'submitter' => $submitterName],
            'medium'
        );
    }

    public function notifyQualityAlert(string $productName, string $issue): void
    {
        $message = "Quality issue detected for {$productName}: {$issue}.";

        $this->sendToRole(
            'Quality Inspector',
            'quality_alert',
            'Quality Alert',
            $message,
            ['product' => $productName, 'issue' => $issue],
            'high'
        );
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)->whereNull('read_at')->count();
    }
}
