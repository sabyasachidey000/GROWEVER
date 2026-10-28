<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'machine_id', 'shift_id',
        'login_time', 'logout_time', 'status', 'remarks',
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function productionRuns()
    {
        return $this->hasMany(ProductionRun::class, 'operator_session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForMachine($query, $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('login_time', $date);
    }

    public function logout()
    {
        $this->update([
            'logout_time' => now(),
            'status' => 'completed',
        ]);
    }

    public function getDurationInMinutes(): int
    {
        if (!$this->logout_time) {
            return Carbon::parse($this->login_time)->diffInMinutes(now());
        }
        return Carbon::parse($this->login_time)->diffInMinutes($this->logout_time);
    }

    public static function getActiveSessionForMachine($machineId)
    {
        return static::where('machine_id', $machineId)
            ->where('status', 'active')
            ->latest('login_time')
            ->first();
    }
}
