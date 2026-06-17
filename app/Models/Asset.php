<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory, Searchable;

    protected $fillable = [
        'asset_no',
        'line_name',
        'machine_name',
        'maker',
        'manufacture_year',
        'machine_rank',
        'machine_photo',
    ];
}
