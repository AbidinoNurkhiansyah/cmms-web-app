<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartRepair extends Model
{
    protected $fillable = [
        'date',
        'spare_part_id',
        'qty',
        'item_repair',
        'rack',
        'pic1_id',
        'pic2_id',
        'pic3_id',
        'file_before',
        'file_after',
        'part_usage',
        'review',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }

    public function pic1()
    {
        return $this->belongsTo(User::class, 'pic1_id');
    }

    public function pic2()
    {
        return $this->belongsTo(User::class, 'pic2_id');
    }

    public function pic3()
    {
        return $this->belongsTo(User::class, 'pic3_id');
    }
}
