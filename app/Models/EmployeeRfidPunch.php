<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeeRfidPunch extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'machine_id', 'rfid_punch_log_id', 'rfid_card_number',
        'device_id', 'punch_type', 'punch_time',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function rawPunchLog()
    {
        return $this->belongsTo(RfidPunchLog::class, 'rfid_punch_log_id');
    }

    public static function activeEmployeeCount(): int
    {
        return DB::table('employee_rfid_punches as current')->join('employees','employees.id','=','current.employee_id')
            ->where('current.punch_type', 'LOGIN')->where('employees.designation_id',6)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('employee_rfid_punches as newer')
                    ->whereColumn('newer.employee_id', 'current.employee_id')
                    ->whereRaw('(newer.punch_time > current.punch_time OR (newer.punch_time = current.punch_time AND newer.id > current.id))');
            })
            ->count();
    }
}
