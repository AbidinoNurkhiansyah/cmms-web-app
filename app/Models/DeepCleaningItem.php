<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeepCleaningItem extends Model
{
    protected $table = 'cmms_deep_cleaning_items';

    protected $fillable = [
        'deep_cleaning_id',
        'itemcheck',
        'description',
        'action',
        'status',
        'before_photo',
        'after_photo'
    ];

    public function deepCleaning()
    {
        return $this->belongsTo(DeepCleaning::class, 'deep_cleaning_id');
    }
}
