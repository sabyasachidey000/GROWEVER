<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_code', 'name', 'category', 'hsn_code',
        'product_category_master_base', 'product_type_master_base',
        'product_type', 'product_type_id', 'unit_of_measure', 'target_cycle_time', 'standard_production_rate',
        'specifications', 'drawing_reference', 'material_grade',
        'weight', 'dimensions', 'is_active',
    ];

    protected $casts = [
        'target_cycle_time' => 'decimal:2',
        'standard_production_rate' => 'decimal:2',
        'weight' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function productionRuns()
    {
        return $this->hasMany(ProductionRun::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function machines()
    {
        return $this->belongsToMany(Machine::class);
    }

    public function rejections()
    {
        return $this->hasMany(Rejection::class);
    }

    public function qualityInspections()
    {
        return $this->hasMany(QualityInspection::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('product_type', $type);
    }
}
