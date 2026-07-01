<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeepCleaningMachineItem extends Model
{
    use HasFactory;

    protected $table = 'cmms_deep_cleaning_machine_items';

    protected $fillable = [
        'machineName',
        'lineName',
        'itemCheck',
        'standard',
    ];
}
