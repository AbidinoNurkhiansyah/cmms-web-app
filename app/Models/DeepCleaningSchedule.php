<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeepCleaningSchedule extends Model
{
    use HasFactory;

    protected $table = 'cmms_deep_cleaning_schedules';

    protected $fillable = [
        'planDate',
        'act_date',
        'NameMachine',
        'machine_no',
        'LineName',
        'items',
        'is_approved',
        'postponed',
    ];

    protected $casts = [
        'planDate' => 'date',
        'act_date' => 'date',
        'items' => 'array',
        'is_approved' => 'boolean',
        'postponed' => 'boolean',
    ];
}
