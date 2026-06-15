<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sky extends Model
{
    protected $table = 'cmms_sky';
    protected $primaryKey = 'no';

    protected $fillable = [
        'date',
        'userId',
        'lokasi',
        'bahaya',
        'countermeasure',
        'resiko',
        'img',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
