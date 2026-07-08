<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Punch extends Model
{
    protected $fillable = [
        'service_request_id', 'user_id',
        'punch_in_at', 'site_location', 'work_description', 'start_photo_path', 
        'punch_out_at', 'finish_photo_path', 'completion_summary',
        'materials_subtotal', 'labour_charge', 'grand_total', 'receipt_number', 'notes',
        'customer_name', 'customer_phone','customer_signature_path', 'status',
    ];

    protected $casts = [
        'punch_in_at'  => 'datetime',
        'punch_out_at' => 'datetime',
        'materials_subtotal' => 'decimal:2',
        'labour_charge'      => 'decimal:2',
        'grand_total'        => 'decimal:2',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /** The worker is a User with role ML. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PunchItem::class);
    }

    /** Human duration between punch in and out, e.g. "2h 05m". */
    public function getDurationLabelAttribute(): ?string
    {
        if (!$this->punch_in_at || !$this->punch_out_at) {
            return null;
        }
        $mins = $this->punch_in_at->diffInMinutes($this->punch_out_at);
        return intdiv($mins, 60) . 'h ' . str_pad($mins % 60, 2, '0', STR_PAD_LEFT) . 'm';
    }
}