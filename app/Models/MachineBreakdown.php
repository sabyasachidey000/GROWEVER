<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineBreakdown extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'machine_id', 'reported_by', 'assigned_to',
        'breakdown_type', 'severity', 'start_time', 'end_time',
        'duration_minutes', 'description', 'root_cause',
        'repair_action', 'spare_parts_used', 'repair_cost', 'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'repair_cost' => 'decimal:2',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(Employee::class, 'reported_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForMachine($query, $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('breakdown_type', $type);
    }

    public function close($repairAction = null, $rootCause = null)
    {
        $this->update([
            'end_time' => now(),
            'duration_minutes' => $this->start_time->diffInMinutes(now()),
            'status' => 'closed',
            'repair_action' => $repairAction ?? $this->repair_action,
            'root_cause' => $rootCause ?? $this->root_cause,
        ]);

        // Set machine status back to idle
        $this->machine->update(['status' => 'running']);
    }

    public function getDowntimeHours(): float
    {
        if ($this->duration_minutes) {
            return round($this->duration_minutes / 60, 2);
        }
        return round($this->start_time->diffInMinutes(now()) / 60, 2);
    }
}
