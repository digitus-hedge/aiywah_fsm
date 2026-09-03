<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAlertSchedule extends Model
{
    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    protected $fillable = [
        'user_id','is_enabled', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
    ];

    protected $casts = [
        'monday'    => 'boolean',
        'tuesday'   => 'boolean',
        'wednesday' => 'boolean',
        'thursday'  => 'boolean',
        'friday'    => 'boolean',
        'saturday'  => 'boolean',
        'sunday'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * True if this schedule allows sending on the given Carbon-ish date
     * (defaults to today). Used by the daily scheduled job.
     */
    public function isEnabledFor($date = null): bool
    {
        $date = $date ?: now();
        $day = strtolower($date->format('l')); // 'monday', 'tuesday', ...

        return (bool) ($this->{$day} ?? true);
    }
}