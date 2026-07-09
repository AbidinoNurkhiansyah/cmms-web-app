<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionSystem extends Model
{
    /** @use HasFactory<\Database\Factories\SuggestionSystemFactory> */
    use HasFactory;

    protected $fillable = [
        'tgl',
        'user_id',
        'ss_title',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
