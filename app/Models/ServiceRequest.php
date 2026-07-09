<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends Model
{
    protected $fillable = [
        'client_id',
        'project_id',
        'service_type_id',
        'reported_by',
        'priority_level',
        'issue_description',
        'internal_remark',
        'status',
        'attachments',
        'assigned_user_id',
        'service_domain_id',
        'dispatched_at',
        'qc_reviewed_at',
        'qc_reviewed_by',
        'rework_notes',
        'warranty_scope',
    ];

    protected $casts = [
        'attachments' => 'array',
        'dispatched_at'  => 'datetime',
        'qc_reviewed_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_type_id');
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function domains()
    {
        return $this->hasMany(ServiceDomain::class, 'service_category_id');
    }

    public function activePunch()
    {
        return $this->hasMany(\App\Models\Punch::class)
            ->whereIn('status', ['draft', 'punched_in'])
            ->latest()
            ->first();
    }

    public function punches()
    {
        return $this->hasMany(\App\Models\Punch::class);
    }

    // Display reference, e.g. "SR-2026-000123"
    public function getRefAttribute(): string
    {
        return 'SR-' . now()->format('Y') . '-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getCodeAttribute(): string
    {
        return 'SR-' . ($this->created_at?->format('Y') ?? now()->year)
            . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /** The submitted punch awaiting / undergoing QC review. */
    public function qcPunch()
    {
        return $this->hasMany(\App\Models\Punch::class)
            ->whereIn('status', ['submitted', 'qc_review'])
            ->latest('punch_out_at')
            ->first();
    }

    public function punch(): HasOne
{
    return $this->hasOne(Punch::class)->latestOfMany();
}
}
