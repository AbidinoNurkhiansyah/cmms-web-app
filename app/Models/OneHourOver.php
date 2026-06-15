<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OneHourOver extends Model
{
    protected $table = 'one_hour_over';

    protected $fillable = [
        'date', 'group_name', 'line', 'machine',
        'problem', 'file_rsa', 'file_rca', 'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
