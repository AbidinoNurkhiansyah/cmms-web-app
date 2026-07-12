<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $table = 'cmms_work_order_request';

    protected $fillable = [
        'date', 'target_date', 'order_type', 'requester', 'department',
        'LineName', 'MachineNo', 'MachineName',
        'problem_description', 'foto_req',
        'pic', 'pic1', 'pic2', 'pic3', 
        'status', 'priority',
        'confirmation_note', 'foto_confirm1', 'foto_confirm2', 'actual_date',
    ];

    protected $casts = [
        'date' => 'date',
        'target_date' => 'date',
        'actual_date' => 'date',
    ];
}
