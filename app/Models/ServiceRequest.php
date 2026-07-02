<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected $casts = [
        'attachments' => 'array',
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
}
