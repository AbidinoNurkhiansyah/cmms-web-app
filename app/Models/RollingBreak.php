<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RollingBreak extends Model
{
    use HasFactory;

    protected $table = 'cmms_rolling_break';

    protected $fillable = [
        'date_input',
        'shift',
        'break_time',
        'fullname',
        'jid_no',
        'notes',
    ];

    protected $casts = [
        'date_input' => 'datetime',
    ];

    /**
     * Get the user associated with the rolling break.
     */
    public function user(): BelongsTo
    {
        // Hubungkan berdasarkan jid_no ke column jid_no di tabel users (jika ada relasinya)
        // cmms_user biasanya sinkron dengan model User.
        return $this->belongsTo(User::class, 'jid_no', 'jid_no');
    }
}
