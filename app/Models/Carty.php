<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carty extends Model
{
    protected $table = 'carty';

    protected $fillable = [
        'Date', 'Shift', 'groupline', 'LineName', 'MachineNo', 'MachineName',
        'equipment', 'classification', 'typeofproblem', 'sparepartName', 'sparepartType',
        'start_time', 'finish_time', 'DownTime', 'worktime', 'stopline',
        'Problem', 'Cause', 'Action', 'Status',
        'filebefore1', 'filebefore2', 'fileafter1', 'fileafter2',
        'PIC', 'pic2', 'pic3', 'pic_repair',
    ];

    protected $casts = [
        'Date' => 'date',
        'DownTime' => 'integer',
        'Shift' => 'integer',
    ];
}
