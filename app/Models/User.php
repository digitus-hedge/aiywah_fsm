<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\HasPermissions;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use LogsActivity;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasPermissions;
    protected $fillable = ['name', 'email', 'country_code', 'phone', 'password', 'role_id', 'fd_grants', 'status', 'can_qc_review'];

    protected $casts = [
        'password'  => 'hashed',
        'fd_grants' => 'array',
        'email_verified_at'   => 'datetime',
        'must_reset_password' => 'boolean',
        'can_qc_review'       => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function serviceDomains()
    {
        return $this->belongsToMany(ServiceDomain::class, 'user_service_domain')
            ->withPivot('service_category_id')
            ->withTimestamps();
    }


    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'user_service_category')
            ->withTimestamps();
    }

    public function punches()
    {
        return $this->hasMany(\App\Models\Punch::class);
    }

    // Service requests dispatched to this worker
    public function assignedServiceRequests()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class, 'assigned_user_id');
    }

    // Is this user a field worker?
    public function isWorker(): bool
    {
        return optional($this->role)->code === 'ML';
    }

    // Two-letter avatar initials, e.g. "Rajesh Kumar" -> "RK"
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name));
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
        return mb_strtoupper($first . $last) ?: '—';
    }

    // "Trade" label derived from the worker's service domains
    public function getTradeLabelAttribute(): ?string
    {
        if (!$this->relationLoaded('serviceDomains') && !$this->exists) {
            return null;
        }
        $names = $this->serviceDomains->pluck('name')->filter()->values();
        return $names->isNotEmpty() ? $names->join(', ') : null;
    }
}
