<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Andon extends Model
{
    protected $table = 'mtc_call';

    protected $fillable = [
        'date_shift', 'date_in', 'time_in', 'line_name', 'machine',
        'shift', 'status', 'stop_info', 'stop_time', 'call_code',
        'finish_time', 'name_pic', 'remark', 'mechanic', 'electric',
        'w_total', 'w_stop', 'man_hours', 'cause_actual', 'preventive', 'hasil_repair',
    ];

    protected $casts = [
        'date_shift'  => 'date',
        'date_in'     => 'date',
        'time_in'     => 'datetime',
        'finish_time' => 'datetime',
        'mechanic'    => 'boolean',
        'electric'    => 'boolean',
    ];
}
