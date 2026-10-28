<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcInspectionItem extends Model
{
    protected $fillable = [
        'inspection_time',
        'machine_no',
        'product',
        'thread_gauge_go',
        'thread_gauge_no_go',
        'concentricity_minor_dia',
        'emt_gauge_after_tapping',
        'burr_on_face',
        'burr_on_thread',
        'thread_flat',
        'tap_mark',
        'offset',
        'angularity',
        'visual_defects',
        'remarks',
        'sample_qty',
        'passed_qty',
        'rejected_qty',
    ];

    public function report()
    {
        return $this->belongsTo(QcInspectionReport::class, 'qc_inspection_report_id');
    }
}
