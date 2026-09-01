<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class WhatsappLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'service_request_id', 'client_id', 'sr_reference', 'recipient',
        'client_name', 'event', 'status', 'template', 'message',
        'payload', 'response', 'wamid', 'error', 'retry_count', 'sent_at',
    ];

    protected $casts = [
        'payload'  => 'array',
        'response' => 'array',
        'sent_at'  => 'datetime',
    ];

    public const STATUS_PENDING   = 'Pending';
    public const STATUS_SENT      = 'Sent';
    public const STATUS_DELIVERED = 'Delivered';
    public const STATUS_FAILED    = 'Failed';

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /* ---------- Scopes ---------- */

    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        return $q->where(function ($w) use ($term) {
            $w->where('sr_reference', 'like', "%{$term}%")
              ->orWhere('client_name', 'like', "%{$term}%")
              ->orWhere('recipient', 'like', "%{$term}%");
        });
    }

    public function scopeEvent($q, ?string $event)
    {
        return $event ? $q->where('event', $event) : $q;
    }

    public function scopeStatus($q, ?string $status)
    {
        return $status ? $q->where('status', $status) : $q;
    }

    public function scopeDateFrom($query, $date)
    {
        return $query->when($date, fn($q) => $q->whereDate('created_at', '>=', $date));
    }
    public function scopeDateTo($query, $date)
    {
        return $query->when($date, fn($q) => $q->whereDate('created_at', '<=', $date));
    }

    /* ---------- Presentation ---------- */

    public function toRowArray(): array
    {
        return [
            'id'        => $this->id,
            'sr'        => $this->sr_reference ?? '—',
            'srId'      => $this->service_request_id,
            'recipient' => $this->recipient,
            'client'    => $this->client_name ?? '—',
            'event'     => $this->event,
            'status'    => $this->status,
            'message'   => $this->message,
            'time'      => optional($this->created_at)->format('d M Y, h:i A'),
        ];
    }
}