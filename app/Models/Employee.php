<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'employee_code', 'name', 'email', 'mobile',
        'rfid_card_number', 'department_id', 'designation_id',
        'emergency_contact', 'emergency_phone', 'date_of_joining',
        'status', 'profile_photo', 'address', 'face_descriptor',
        'face_machine_id','shift',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'face_descriptor' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function faceMachine()
    {
        return $this->belongsTo(Machine::class, 'face_machine_id');
    }

    public function operatorSessions()
    {
        return $this->hasMany(OperatorSession::class);
    }

    public function productionRuns()
    {
        return $this->hasMany(ProductionRun::class, 'operator_id');
    }

    public function reportedBreakdowns()
    {
        return $this->hasMany(MachineBreakdown::class, 'reported_by');
    }

    public function assignedBreakdowns()
    {
        return $this->hasMany(MachineBreakdown::class, 'assigned_to');
    }

    public function qualityInspections()
    {
        return $this->hasMany(QualityInspection::class, 'inspector_id');
    }

    public function submittedApprovals()
    {
        return $this->hasMany(ProductionApproval::class, 'submitted_by');
    }

    public function reviewedApprovals()
    {
        return $this->hasMany(ProductionApproval::class, 'reviewed_by');
    }

    public function rejections()
    {
        return $this->hasMany(Rejection::class, 'operator_id');
    }

    public function shiftAssignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    public function rfidLogs()
    {
        return $this->hasMany(RfidLog::class);
    }

    public function rfidPunches()
    {
        return $this->hasMany(EmployeeRfidPunch::class);
    }

    public function faceAttendanceLogs()
    {
        return $this->hasMany(FaceAttendanceLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithRfid($query)
    {
        return $query->whereNotNull('rfid_card_number');
    }

    public function scopeWithFaceDescriptor($query)
    {
        return $query->whereNotNull('face_descriptor');
    }

    public static function findByRfid($rfidNumber)
    {
        return static::where('rfid_card_number', $rfidNumber)->first();
    }
}
