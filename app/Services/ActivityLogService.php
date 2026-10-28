<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public function log(string $description, $subject = null, string $event = null, string $logName = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'event' => $event,
            'properties' => [],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public function getLogsForUser(int $userId, int $limit = 50)
    {
        return ActivityLog::forUser($userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getLogsForSubject(string $subjectType, int $subjectId)
    {
        return ActivityLog::forSubject($subjectType, $subjectId)
            ->latest()
            ->get();
    }

    public function getTodayLogs(): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::forDate(today())
            ->with('user')
            ->latest()
            ->get();
    }
}
