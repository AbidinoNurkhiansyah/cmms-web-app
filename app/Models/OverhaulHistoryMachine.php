<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverhaulHistoryMachine extends Model
{
    protected $table = 'cmms_oh_history_machines';

    protected $fillable = [
        'asset_id',
        'tgl_berlaku',
        'row_date',
        'problem',
        'cause',
        'corrective_action',
        'part_change',
        'pic_id',
        'frequency',
    ];

    protected $casts = [
        'tgl_berlaku' => 'date',
        'row_date' => 'date',
        'part_change' => 'array',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
