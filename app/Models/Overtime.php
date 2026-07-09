<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $fillable = ['user_id', 'date', 'hours_1', 'hours_2'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
