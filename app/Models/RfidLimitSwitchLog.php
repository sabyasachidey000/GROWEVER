<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidLimitSwitchLog extends Model
{
    protected $table = 'rfid_limit_switch_logs';

    protected $fillable = [
        'device_id',
        'sensor',
        'user_id',
        'status',
        'counter',
    ];
}