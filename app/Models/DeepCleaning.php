<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeepCleaning extends Model
{
    protected $table = 'cmms_deep_cleanings';

    protected $fillable = [
        'Date', 'LineName', 'MachineNo', 'MachineName',
        'pics', 'description'
    ];

    protected $casts = [
        'Date' => 'date',
        'pics' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(DeepCleaningItem::class, 'deep_cleaning_id');
    }

    public function spareparts()
    {
        return $this->hasMany(DeepCleaningSparepart::class, 'deep_cleaning_id');
    }
}
