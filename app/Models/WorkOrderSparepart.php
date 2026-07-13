<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderSparepart extends Model
{
    use HasFactory;

    protected $table = 'cmms_work_order_spareparts';

    protected $fillable = [
        'work_order_id',
        'sparepart_id',
        'qty',
        'remarks',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function sparepart()
    {
        return $this->belongsTo(SparePart::class, 'sparepart_id');
    }
}
