<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carty extends Model
{
    protected $table = 'carty';

    protected $fillable = [
        'Date', 'groupline', 'LineName', 'MachineNo', 'MachineName',
        'DownTime', 'Problem', 'Action', 'Status', 'Shift',
        'PIC', 'pic_repair', 'start_time', 'finish_time',
    ];

    protected $casts = [
        'Date' => 'date',
        'DownTime' => 'integer',
        'Shift' => 'integer',
    ];
}
