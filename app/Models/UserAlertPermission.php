<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAlertPermission extends Model
{
    protected $fillable = [
        'user_id', 'alert_type_id', 'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function alertType()
    {
        return $this->belongsTo(AlertType::class);
    }
}