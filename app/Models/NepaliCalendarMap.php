<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NepaliCalendarMap extends Model
{
    protected $fillable = ['year', 'months'];

    protected $casts = [
        'months' => 'array',
    ];
}
