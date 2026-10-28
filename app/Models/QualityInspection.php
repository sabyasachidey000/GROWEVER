<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'production_run_id', 'product_id', 'inspector_id',
        'machine_id', 'shift_id', 'inspection_date', 'inspection_time', 'inspection_type',
        'sample_size', 'passed_quantity', 'failed_quantity',
        'acceptance_rate', 'measurements', 'remarks', 'result',
        'production_signature', 'checked_by',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'measurements' => 'array',
        'acceptance_rate' => 'decimal:2',
    ];

    public function productionRun()
    {
        return $this->belongsTo(ProductionRun::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inspector()
    {
        return $this->belongsTo(Employee::class, 'inspector_id');
    }

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
        return $query->where('inspection_date', $date);
    }

    public function scopePassed($query)
    {
        return $query->where('result', 'pass');
    }

    public function scopeFailed($query)
    {
        return $query->where('result', 'fail');
    }

    public function getAcceptanceRate(): float
    {
        if ($this->sample_size == 0) {
            return 0;
        }

        return round(($this->passed_quantity / $this->sample_size) * 100, 2);
    }

    public function getFailureRate(): float
    {
        if ($this->sample_size == 0) {
            return 0;
        }

        return round(($this->failed_quantity / $this->sample_size) * 100, 2);
    }
}
