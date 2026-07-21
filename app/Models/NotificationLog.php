<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'service_request_id',
        'event',
        'title',
        'message',
        'from_status',
        'to_status',
        'caused_by',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caused_by');
    }

    /** Scope: only unread */
    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }
}