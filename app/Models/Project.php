<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name', 'abbreviation', 'head'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bureau(): BelongsTo
    {
        return $this->belongsTo(Bureau::class);
    }
}
