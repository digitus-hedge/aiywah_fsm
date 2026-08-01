<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class SlaMatrix extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'sla_matrix';

    protected $fillable = [
        'priority_id', 'response_time', 'assignment_time',
        'resolution_time', 'alert_percentage', 'status',
    ];
    protected $appends = ['approve_minutes', 'dispatch_minutes', 'qc_minutes'];

    public function getApproveMinutesAttribute(): int   { return (int) $this->response_time * 60; }
    public function getDispatchMinutesAttribute(): int  { return (int) $this->assignment_time * 60; }
    public function getQcMinutesAttribute(): int        { return (int) $this->resolution_time * 60; }
    protected $casts = [
        'status'           => 'boolean',
        'alert_percentage' => 'decimal:2',
    ];

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }
}
