<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;
class ServiceRequestReschedule extends Model
{
    use LogsActivity;
    protected $fillable = [
        'service_request_id', 'user_id',
        'previous_eta_at', 'new_eta_at', 'reason', 'from_status',
    ];

    protected $casts = [
        'previous_eta_at' => 'datetime',
        'new_eta_at'      => 'datetime',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}