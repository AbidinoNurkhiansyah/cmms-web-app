<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSkill extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'skill_name',
        'actual_level',
        'target_level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
