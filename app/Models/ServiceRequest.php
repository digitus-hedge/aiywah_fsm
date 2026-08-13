<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\LogsActivity;
class ServiceRequest extends Model
{
    use LogsActivity;
    protected $fillable = [
        'client_id',
        'project_id',
        'service_type_id',
        'reported_by',
        'priority_level',
        'issue_description',
        'internal_remark',
        'status',
        'created_by',
        'attachments',
        'assigned_user_id',
        'service_domain_id',
        'dispatched_at',
        'qc_reviewed_at',
        'qc_reviewed_by',
        'rework_notes',
        'warranty_scope',
        'invoice_code',
        'invoice_total',
        'invoice_path',
        'invoice_submitted_at',
        'invoice_uploaded_by',
        'hop_approved_at',
        'hop_approved_by',
        'erp_quote_ref',
        'quote_path',
        'quote_submitted_at',
        'client_approved_at',
        'eta_at',
        'accepted_at',
        'hold_reason',
        'held_at',
        'approved_at',
        'qc_updated',
        'performance_score',
        'evaluation_comment',
        'feedback_submitted_at',
        'portal_link_sent_at',
        'assigned_se',
        'reallocate',
        'reallocate_user_id',
        'reallocated_submit_at',
        'relocation_remarks'
    ];

    protected $casts = [
        'attachments' => 'array',
        'invoice_total' => 'decimal:2',
        'invoice_submitted_at' => 'datetime',
        'hop_approved_at' => 'datetime',
        'quote_submitted_at' => 'datetime',
        'client_approved_at' => 'datetime',
        'approved_at'    => 'datetime',
        'dispatched_at'  => 'datetime',
        'qc_reviewed_at' => 'datetime',
        'eta_at'         => 'datetime',
        'accepted_at'    => 'datetime',
        'held_at'        => 'datetime',
        'qc_updated'    => 'datetime',

        'performance_score'     => 'integer',
        'feedback_submitted_at' => 'datetime',
        'portal_link_sent_at' => 'datetime',
        'reallocated_submit_at' => 'datetime',
    ];


    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function reallocateUser()
{
    return $this->belongsTo(User::class, 'reallocate_user_id');
}

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_type_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
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
        return $this->hasMany(Punch::class)
            ->whereIn('status', ['draft', 'punched_in'])
            ->latest()
            ->first();
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(ServiceDomain::class, 'service_domain_id');
    }

    public function punches()
    {
        return $this->hasMany(\App\Models\Punch::class);
    }
    // Display reference, e.g. "SR-2026-000123"
    public function getRefAttribute(): string
    {
        return 'SR-' . now()->format('Y') . '-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getCodeAttribute(): string
    {
        return 'SR-' . ($this->created_at?->format('Y') ?? now()->year)
            . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /** The submitted punch awaiting / undergoing QC review. */
    public function getQcPunchAttribute()
    {
        return $this->punches
            ->whereIn('status', ['submitted', 'qc_review'])
            ->sortByDesc('punch_out_at')
            ->first();
    }



    public function punch(): HasOne
    {
        return $this->hasOne(Punch::class)->latestOfMany();
    }
    public function createdBy()    { return $this->belongsTo(User::class, 'created_by'); }
    public function qcReviewedBy() { return $this->belongsTo(User::class, 'qc_reviewed_by'); }

    public function reschedules(): HasMany
    {
        return $this->hasMany(ServiceRequestReschedule::class)->latest('id');
    }

    /** The most recent reschedule, or null. */
    public function getLastRescheduleAttribute(): ?ServiceRequestReschedule
    {
        return $this->reschedules->first();
    }

    public function getRescheduleCountAttribute(): int
    {
        return $this->reschedules->count();
    }
    public function assignedSe()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_se');
    }
}
