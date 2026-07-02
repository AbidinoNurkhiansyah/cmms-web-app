<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverhaulStep extends Model
{
    protected $table = 'cmms_oh_steps';

    protected $fillable = [
        'overhaul_id',
        'step_repair',
        'minutes',
        'obstacle',
    ];

    public function overhaul()
    {
        return $this->belongsTo(Overhaul::class, 'overhaul_id');
    }
}
