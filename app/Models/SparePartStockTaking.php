<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparePartStockTaking extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_stock',
        'spare_part_id',
        'in_qty',
        'out_qty',
        'last_stock',
        'check_stock',
        'remark',
    ];

    protected $casts = [
        'date_stock' => 'date',
        'in_qty' => 'integer',
        'out_qty' => 'integer',
        'last_stock' => 'integer',
        'check_stock' => 'integer',
    ];

    /**
     * Get the spare part associated with this stock taking record.
     */
    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}
