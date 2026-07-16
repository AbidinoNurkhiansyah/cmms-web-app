<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carty extends Model
{
    protected $table = 'carty';

    protected $fillable = [
        'Date', 'Shift', 'groupline', 'LineName', 'MachineNo', 'MachineName',
        'typeofproblem', 'sparepartName', 'sparepartQty',
        'start_time', 'finish_time', 'DownTime', 'worktime',
        'Problem', 'Cause', 'Action', 'Status',
        'filebefore1', 'filebefore2', 'fileafter1', 'fileafter2',
        'PIC', 'pics',
    ];

    protected $casts = [
        'Date' => 'date',
        'DownTime' => 'integer',
        'Shift' => 'integer',
        'pics' => 'array',
    ];

    public function scopeAuthorized($query, $user)
    {
        return $query;
    }

    public function spareParts()
    {
        return $this->belongsToMany(SparePart::class)->withPivot('qty')->withTimestamps();
    }
}
