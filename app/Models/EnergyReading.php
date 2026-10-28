<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyReading extends Model
{
    protected $table = 'energy_readings';

    protected $guarded = [];

    protected $casts = [
        'timestamp' => 'datetime',
        'wh_import' => 'decimal:3',
        'watt_sum' => 'decimal:3',
        'current_sum' => 'decimal:3',
        'voltage_avg' => 'decimal:3',
        'frequency' => 'decimal:3',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
