<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'template_name', 'trigger_event', 'description',
        'body', 'variables', 'status',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'variables' => 'array',
    ];
}
