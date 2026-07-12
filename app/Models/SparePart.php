<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class SparePart extends Model
{
    /** @use HasFactory<\Database\Factories\SparePartFactory> */
    use HasFactory, Searchable;

    protected $fillable = [
        'group',
        'rank',
        'group_id',
        'part_number',
        'part_name',
        'no_rack',
        'last_stock',
        'maker',
        'machine',
        'status',
        'part_photo',
        'use_qty',
        'price_idr',
        'repair_stock',
        'repair_rack',
        'manual_doc',
    ];

    public function carties()
    {
        return $this->belongsToMany(Carty::class)->withPivot('qty')->withTimestamps();
    }

    public function machineSpareParts()
    {
        return $this->hasMany(MachineSparePart::class);
    }
}
