<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverhaulSparepart extends Model
{
    protected $table = 'cmms_oh_spareparts';

    protected $fillable = [
        'overhaul_id',
        'type',
        'qty',
        'maker',
        'remarks',
    ];

    public function overhaul()
    {
        return $this->belongsTo(Overhaul::class, 'overhaul_id');
    }
}
