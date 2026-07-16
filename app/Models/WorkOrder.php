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

    public function scopeAuthorized($query, $user)
    {
        if ($user->hasRole(\App\Models\User::ROLE_OPERATOR)) {
            return $query->where('LineName', $user->line_name);
        }
        return $query;
    }

    public function spareparts()
    {
        return $this->hasMany(WorkOrderSparepart::class, 'work_order_id');
    }
}
