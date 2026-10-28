<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rejection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'production_run_id', 'machine_id', 'product_id',
        'operator_id', 'shift_id', 'rejection_reason',
        'quantity', 'description', 'corrective_action', 'rejection_date',
    ];

    protected $casts = [
        'rejection_date' => 'date',
    ];

    public function productionRun()
    {
        return $this->belongsTo(ProductionRun::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function operator()
    {
        return $this->belongsTo(Employee::class, 'operator_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('rejection_date', $date);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForMachine($query, $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('rejection_reason', $reason);
    }

    public static function getAnalysisByReason($startDate, $endDate)
    {
        return static::whereBetween('rejection_date', [$startDate, $endDate])
            ->select('rejection_reason')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('COUNT(*) as occurrence_count')
            ->groupBy('rejection_reason')
            ->orderByDesc('total_quantity')
            ->get();
    }
}
