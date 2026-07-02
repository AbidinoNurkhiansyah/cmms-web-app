<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpmChecksheet extends Model
{
    protected $table = 'cmms_tpm_checksheet';

    protected $guarded = ['id'];

    protected $casts = [
        'checked_date' => 'date',
        'gata_mm' => 'decimal:2',
        'clamp_kN' => 'decimal:2',
        'run_out_kelurusan' => 'decimal:2',
        'run_out_putaran' => 'decimal:2',
    ];

    /**
     * Get the asset associated with the checksheet.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'machineNo', 'asset_no');
    }
}
