<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecksheetItem extends Model
{
    protected $table = 'cmms_cs_items';

    protected $fillable = [
        'asset_no', 'machine_name', 'line_name', 'item_check',
        'standard', 'method', 'periode', 'photo_path', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
