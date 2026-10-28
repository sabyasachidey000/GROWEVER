<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionRun extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'machine_id', 'operator_id', 'product_id', 'shift_id',
        'operator_session_id', 'start_time', 'end_time',
        'runtime_minutes', 'weight', 'remarks', 'status','total_actual_coun', 
        'total_actual_count', 
        'rejected',           
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'weight' => 'decimal:2',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function operator()
    {
        return $this->belongsTo(Employee::class, 'operator_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function operatorSession()
    {
        return $this->belongsTo(OperatorSession::class);
    }

    public function approval()
    {
        return $this->hasOne(ProductionApproval::class);
    }

    public function rejections()
    {
        return $this->hasMany(Rejection::class);
    }

    public function qualityInspections()
    {
        return $this->hasMany(QualityInspection::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('start_time', $date);
    }

    public function scopeForMachine($query, $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    public function scopeForShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function complete()
    {
        $this->update([
            'end_time' => now(),
            'status' => 'completed',
            'runtime_minutes' => $this->start_time->diffInMinutes(now()),
        ]);
    }
}
