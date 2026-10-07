<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT    = 'sent';
    public const STATUS_FAILED  = 'failed';

    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /* ── Relationships ── */

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /* ── Query scopes (mirror WhatsappLog) ── */

    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn ($x) => $x->where(function ($w) use ($term) {
            $w->where('sr_reference', 'like', "%{$term}%")
              ->orWhere('recipient', 'like', "%{$term}%")
              ->orWhere('client_name', 'like', "%{$term}%");
        }));
    }

    public function scopeEvent($q, ?string $event)
    {
        return $q->when($event, fn ($x) => $x->where('event', $event));
    }

    public function scopeStatus($q, ?string $status)
    {
        return $q->when($status, fn ($x) => $x->where('status', strtolower($status)));
    }

    public function scopeDateFrom($q, ?string $date)
    {
        return $q->when($date, fn ($x) => $x->whereDate('created_at', '>=', $date));
    }

    public function scopeDateTo($q, ?string $date)
    {
        return $q->when($date, fn ($x) => $x->whereDate('created_at', '<=', $date));
    }

    /* ── Row shape for the frontend ── */

    public function toRowArray(): array
    {
        return [
            'id'        => $this->id,
            'srId'      => $this->service_request_id,
            'sr'        => $this->sr_reference ?: '—',
            'recipient' => $this->recipient,
            'client'    => $this->client_name ?: '—',
            'event'     => $this->event,
            'status'    => ucfirst($this->status),
            'subject'   => $this->subject,
            'message'   => $this->message,
            'error'     => $this->error,
            'time'      => optional($this->created_at)->format('d M Y, h:i A'),
        ];
    }
}