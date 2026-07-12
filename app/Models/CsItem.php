<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsItem extends Model
{
    use HasFactory;

    protected $table = 'cmms_cs_items';

    protected $fillable = [
        'asset_no',
        'machine_name',
        'line_name',
        'item_check',
        'standard',
        'method',
        'periode',
        'photo_path',
        'sort_order',
        'is_active',
    ];

    /**
     * Get the asset associated with the checksheet item.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_no', 'asset_no');
    }
}
