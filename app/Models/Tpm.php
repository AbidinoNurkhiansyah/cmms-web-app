<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tpm extends Model
{
    protected $table = 'cmms_tpm';

    protected $fillable = [
        'date', 'LineName', 'MachineNo', 'MachineName',
        'pic', 'status', 'photo_before', 'photo_after',
        'description', 'result',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
