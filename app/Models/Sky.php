<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sky extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'jid_no');
    }
}
