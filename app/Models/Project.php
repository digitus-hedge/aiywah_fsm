<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- Import the Trait
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use SoftDeletes; // <-- Use the Trait inside your class
    protected $fillable = [
        'client_id',
        'project_name',
        'project_code',
        'site_name',
        'site_address',
        'completion_date',
        'warranty_end_date',
        'status',
        'warranty_id'
    ];

    protected $casts = [
        'completion_date'   => 'date',
        'warranty_end_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }
   
    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }
}
