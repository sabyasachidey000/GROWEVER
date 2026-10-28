<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_run_id', 'submitted_by', 'reviewed_by',
        'status', 'review_comments', 'rejection_reason',
        'submitted_at', 'reviewed_at', 'is_locked',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function productionRun()
    {
        return $this->belongsTo(ProductionRun::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(Employee::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function approve($userId, $comments = null,$total_reject=0,$total_actual_count=0)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'review_comments' => $comments,
            
            'is_locked' => true,
        ]);
        /*echo $total_actual_count;
        die();*/

        $this->productionRun->update(['status' => 'completed','total_actual_count'=>$total_actual_count,
            'rejected' =>$total_reject]);
    }

    public function reject($userId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
