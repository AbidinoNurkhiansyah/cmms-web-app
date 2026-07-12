<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsDocno extends Model
{
    use HasFactory;

    protected $table = 'cmms_cs_docnos';

    protected $fillable = [
        'asset_no',
        'doc_no',
        'item_revisi',
        'keterangan',
        'tanggal_revisi',
    ];

    protected $casts = [
        'tanggal_revisi' => 'date',
    ];

    /**
     * Get the asset associated with the docno.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_no', 'asset_no');
    }
}
