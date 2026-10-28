<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceAttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'machine_id', 'action', 'confidence',
        'distance', 'ip_address', 'raw_descriptor',
    ];

    protected $casts = [
        'confidence' => 'decimal:4',
        'distance' => 'decimal:4',
        'raw_descriptor' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
