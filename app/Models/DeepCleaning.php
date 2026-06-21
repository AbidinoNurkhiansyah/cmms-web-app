<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeepCleaning extends Model
{
    protected $table = 'cmms_deep_cleanings';

    protected $fillable = [
        'Date', 'LineName', 'MachineNo', 'MachineName',
        'pics', 'status', 'before_photo', 'after_photo',
        'description', 'itemcheck', 'action',
        'sparepart_id', 'sparepart_qty'
    ];

    protected $casts = [
        'Date' => 'date',
        'pics' => 'array',
    ];
}
