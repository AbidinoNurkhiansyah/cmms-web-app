<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDescription extends Model
{
    protected $fillable = ['team', 'description', 'units'];

    protected $casts = [
        'units' => 'array',
    ];
}
