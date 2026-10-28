<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidPunchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'device_id', 'uid', 'punch_time', 'ip_address',
        'employee_code', 'employee_name', 'department_name', 'designation_name',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function businessPunch()
    {
        return $this->hasOne(EmployeeRfidPunch::class);
    }
}
