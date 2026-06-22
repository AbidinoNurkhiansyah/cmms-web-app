<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeepCleaningSparepart extends Model
{
    protected $table = 'cmms_deep_cleaning_spareparts';

    protected $fillable = [
        'deep_cleaning_id',
        'sparepart_id',
        'qty',
        'itemcheck'
    ];

    public function deepCleaning()
    {
        return $this->belongsTo(DeepCleaning::class, 'deep_cleaning_id');
    }
}
