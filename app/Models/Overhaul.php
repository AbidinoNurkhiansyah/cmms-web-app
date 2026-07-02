<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overhaul extends Model
{
    protected $table = 'cmms_oh_web';

    protected $fillable = [
        'date', 'start_time', 'end_time',
        'LineName', 'MachineNo', 'MachineName', 'asset_no',
        'PIC', 'pic1', 'pic2', 'pic3', 
        'description', 'problem', 'status', 'repair_time', 'work_time',
        'explanation', 'next_improvement', 'yokotenkai',
        'photo_before_1', 'photo_after_1', 'photo_before_2', 'photo_after_2'
    ];

    protected $casts = [
        'date'         => 'date',
        'start_time'   => 'datetime',
        'end_time'     => 'datetime',
        'repair_time'  => 'double',
        'work_time'    => 'double',
    ];

    public function steps()
    {
        return $this->hasMany(OverhaulStep::class, 'overhaul_id');
    }

    public function spareparts()
    {
        return $this->hasMany(OverhaulSparepart::class, 'overhaul_id');
    }
}
