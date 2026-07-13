<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checksheet extends Model
{
    protected $table = 'cmms_cs_trx';

    protected $fillable = [
        'doc_no', 'date', 'asset_no', 'pic_sl', 'cs_item_id',
        'result', 'approval_stl', 'apv_prod', 'apv_week',
        'apv_month', 'approval_mtc', 'keterangan',
    ];

    protected $casts = [
        'date'      => 'date',
        'apv_prod'  => 'boolean',
        'apv_week'  => 'boolean',
        'apv_month' => 'boolean',
    ];
}
