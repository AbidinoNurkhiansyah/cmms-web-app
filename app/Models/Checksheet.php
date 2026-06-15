<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checksheet extends Model
{
    protected $table = 'cmms_cs_trx';

    protected $fillable = [
        'noDoc', 'date', 'picSL', 'assetNo', 'itemsId',
        'result', 'approval_stl', 'apvProd', 'apvWeek',
        'apvMonth', 'approval_mtc', 'keterangan',
    ];

    protected $casts = [
        'date'     => 'date',
        'apvProd'  => 'boolean',
        'apvWeek'  => 'boolean',
        'apvMonth' => 'boolean',
    ];
}
