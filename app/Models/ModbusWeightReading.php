<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModbusWeightReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'weight',
        'source',
        'plc_ip',
        'processed',
        'raw_response',
    ];

    protected $casts = [
        'weight' => 'integer',
        'processed' => 'boolean',
    ];

    /**
     * Machine that this reading belongs to.
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
