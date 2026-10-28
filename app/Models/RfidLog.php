<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfid_card_number', 'employee_id', 'machine_id',
        'action', 'raw_data', 'reader_id', 'ip_address',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForMachine($query, $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public static function logSwipe($rfidNumber, $action, $machineId = null, $rawData = null, $readerId = null)
    {
        $employee = Employee::findByRfid($rfidNumber);

        return static::create([
            'rfid_card_number' => $rfidNumber,
            'employee_id' => $employee?->id,
            'machine_id' => $machineId,
            'action' => $action,
            'raw_data' => $rawData,
            'reader_id' => $readerId,
            'ip_address' => request()?->ip(),
        ]);
    }
}
