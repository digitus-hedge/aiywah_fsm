<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PunchPhoto;
use App\Traits\LogsActivity;
class Punch extends Model
{
    use LogsActivity;
   protected $fillable = [
        'service_request_id', 'user_id',
        'punch_in_at', 'site_location', 'work_description', 'start_photo_path',
        'punch_in_lat', 'punch_in_lng', 'punch_in_accuracy', 'punch_in_address',
        'punch_out_at', 'finish_photo_path', 'completion_summary',
        'punch_out_lat', 'punch_out_lng', 'punch_out_accuracy', 'punch_out_address',
        'materials_subtotal', 'labour_charge', 'grand_total', 'notes',
        'customer_name', 'customer_phone', 'customer_signature_path', 'status',
        'signature_lat', 'signature_lng', 'signature_accuracy', 'signature_address', 'signed_at',
    ];

    protected $casts = [
        'punch_in_at'  => 'datetime',
        'punch_out_at' => 'datetime',
        'signed_at'    => 'datetime',
        'materials_subtotal' => 'decimal:2',
        'labour_charge'      => 'decimal:2',
        'grand_total'        => 'decimal:2',
        'punch_in_lat'  => 'float', 'punch_in_lng'  => 'float', 'punch_in_accuracy'  => 'float',
        'punch_out_lat' => 'float', 'punch_out_lng' => 'float', 'punch_out_accuracy' => 'float',
        'signature_lat' => 'float', 'signature_lng' => 'float', 'signature_accuracy' => 'float',
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
        return $this->hasMany(Punchitem::class);
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
    public function photos(): HasMany
    {
        return $this->hasMany(PunchPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function beforePhotos(): HasMany
    {
        return $this->photos()->where('type', 'before');
    }

    public function afterPhotos(): HasMany
    {
        return $this->photos()->where('type', 'after');
    }

     /** Google Maps link for a captured point, or null. */
    protected function mapLink(?float $lat, ?float $lng): ?string
    {
        return ($lat && $lng) ? "https://maps.google.com/?q={$lat},{$lng}" : null;
    }

    public function getPunchInMapUrlAttribute(): ?string
    {
        return $this->mapLink($this->punch_in_lat, $this->punch_in_lng);
    }

    public function getPunchOutMapUrlAttribute(): ?string
    {
        return $this->mapLink($this->punch_out_lat, $this->punch_out_lng);
    }

    public function getSignatureMapUrlAttribute(): ?string
    {
        return $this->mapLink($this->signature_lat, $this->signature_lng);
    }
}