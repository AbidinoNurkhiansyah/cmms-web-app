<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overhaul extends Model
{
    protected $table = 'cmms_oh_web';

    protected $fillable = [
        'date_request', 'date_plan', 'date_start', 'date_finish',
        'LineName', 'MachineNo', 'MachineName', 'description',
        'pic', 'status', 'result', 'photo_before', 'photo_after',
    ];

    protected $casts = [
        'date_request' => 'date',
        'date_plan'    => 'date',
        'date_start'   => 'date',
        'date_finish'  => 'date',
    ];
}
