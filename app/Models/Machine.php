<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'machine_code', 'name', 'type', 'operation_type',
        'manufacturer', 'model_number', 'serial_number',
        'plc_model', 'plc_ip_address', 'location',
        'production_capacity_per_hour', 'production_capacity_per_day',
        'specifications', 'installation_date', 'last_maintenance_date',
        'status', 'is_active', 'machine_ip', 'rfid_device_id','cycle_count',
    ];

    protected $casts = [
        'production_capacity_per_hour' => 'decimal:2',
        'production_capacity_per_day' => 'decimal:2',
        'installation_date' => 'date',
        'last_maintenance_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function operatorSessions()
    {
        return $this->hasMany(OperatorSession::class);
    }

    public function productionRuns()
    {
        return $this->hasMany(ProductionRun::class);
    }

    public function breakdowns()
    {
        return $this->hasMany(MachineBreakdown::class);
    }

    public function activeBreakdown()
    {
        return $this->hasOne(MachineBreakdown::class)->where('status', '!=', 'closed');
    }

    public function rejections()
    {
        return $this->hasMany(Rejection::class);
    }

    public function qualityInspections()
    {
        return $this->hasMany(QualityInspection::class);
    }

    public function shiftAssignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    public function oeeRecords()
    {
        return $this->hasMany(OeeRecord::class);
    }

    public function energyReadings()
    {
        return $this->hasMany(EnergyReading::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeIdle($query)
    {
        return $query->where('status', 'idle');
    }

    public function scopeOnBreakdown($query)
    {
        return $query->where('status', 'breakdown');
    }

    public function getUtilizationForDate($date)
    {
        $productionTime = $this->productionRuns()
            ->whereDate('start_time', $date)
            ->sum('runtime_minutes');

        $plannedTime = 480; // 8 hours default
        return $plannedTime > 0 ? round(($productionTime / $plannedTime) * 100, 2) : 0;
    }
}
