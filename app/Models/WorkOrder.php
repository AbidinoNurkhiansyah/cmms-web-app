<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $table = 'cmms_work_order_request';

    protected $fillable = [
        'date', 'LineName', 'MachineNo', 'MachineName',
        'problem_description', 'pic', 'status', 'priority',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
