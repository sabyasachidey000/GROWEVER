<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'start_time', 'end_time',
        'break_start', 'break_end', 'is_night_shift', 'is_active',
    ];

    protected $casts = [
        'is_night_shift' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function operatorSessions()
    {
        return $this->hasMany(OperatorSession::class);
    }

    public function productionRuns()
    {
        return $this->hasMany(ProductionRun::class);
    }

    public function shiftAssignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    public function rejections()
    {
        return $this->hasMany(Rejection::class);
    }

    public function oeeRecords()
    {
        return $this->hasMany(OeeRecord::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getCurrentShiftForTime($time = null)
    {
        $time = $time ?? now();
        $startTime = $this->start_time;
        $endTime = $this->end_time;

        // Handle night shifts that cross midnight
        if ($this->is_night_shift) {
            return $time->format('H:i:s') >= $startTime || $time->format('H:i:s') < $endTime;
        }

        return $time->format('H:i:s') >= $startTime && $time->format('H:i:s') < $endTime;
    }

    public static function getCurrentShift($time = null)
    {
        $time = $time ?? now();
        return static::active()->get()->first(function ($shift) use ($time) {
            return $shift->getCurrentShiftForTime($time);
        });
    }

    public function getDurationInMinutes(): int
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        $diff = $end - $start;
        if ($diff < 0) {
            $diff += 86400; // Add 24 hours for night shifts
        }
        return (int) ($diff / 60);
    }
}
