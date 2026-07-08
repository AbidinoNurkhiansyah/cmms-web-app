<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineSparePart extends Model
{
    protected $fillable = ['spare_part_id', 'line', 'asset_no', 'machine'];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
