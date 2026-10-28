<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcInspectionReport extends Model
{
    protected $fillable = [
        'inspection_date',
        'shift',
        'product_line',
        'qc_person',
        'production_signature',
        'checked_by',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(QcInspectionItem::class, 'qc_inspection_report_id');
    }
}
