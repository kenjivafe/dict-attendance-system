<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'shapes'
    ];

    protected $casts = [
        'shapes' => 'array', // Ensures JSON decoding when retrieved
    ];
}
