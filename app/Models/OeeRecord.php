<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OeeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id', 'shift_id', 'record_date',
        'planned_production_time', 'runtime',
        'actual_output', 'ideal_output',
        'total_quantity', 'good_quantity',
        'availability', 'performance', 'quality_rate', 'oee',
    ];

    protected $casts = [
        'availability' => 'decimal:4',
        'performance' => 'decimal:4',
        'quality_rate' => 'decimal:4',
        'oee' => 'decimal:4',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('record_date', $date);
    }

    public function scopeForMachine($query, $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    public function getAvailability(): float
    {
        return round((float) $this->availability * 100, 2);
    }

    public function getPerformance(): float
    {
        return round((float) $this->performance * 100, 2);
    }

    public function getQuality(): float
    {
        return round((float) $this->quality_rate * 100, 2);
    }

    public function getOverallOee(): float
    {
        return round((float) $this->oee * 100, 2);
    }
}
