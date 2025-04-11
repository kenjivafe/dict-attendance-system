<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'time_in_am', 
        'time_out_am',
        'time_in_pm', 
        'time_out_pm',
        'undertime_hours', 
        'undertime_minutes',
        'checkpoint_id',
        'latitude',
        'longitude'
    ];

    protected $appends = ['computed_undertime'];

    public function getComputedUndertimeAttribute()
    {
        $expectedMinutes = 8 * 60; // 8 hours = 480 minutes
        $workedMinutes = 0;

        if ($this->time_in_am && $this->time_out_am) {
            $workedMinutes += Carbon::parse($this->time_in_am)->diffInMinutes(Carbon::parse($this->time_out_am));
        }

        if ($this->time_in_pm && $this->time_out_pm) {
            $workedMinutes += Carbon::parse($this->time_in_pm)->diffInMinutes(Carbon::parse($this->time_out_pm));
        }

        $undertimeMinutes = max(0, $expectedMinutes - $workedMinutes); // Prevent negative values

        return [
            'hours' => intdiv($undertimeMinutes, 60),
            'minutes' => $undertimeMinutes % 60,
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            $undertime = $attendance->computed_undertime;
            $attendance->undertime_hours = $undertime['hours'];
            $attendance->undertime_minutes = $undertime['minutes'];
        });
    }

    public function getUndertimeAttribute()
    {
        return "{$this->undertime_hours} hrs and {$this->undertime_minutes} mins";
    }

    protected $casts = [
        'date' => 'date', // Ensures date is a Carbon instance
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }

    protected function month(): Attribute
    {
        return Attribute::get(fn () => $this->date->format('F Y')); // Example: "February 2025"
    }
}
