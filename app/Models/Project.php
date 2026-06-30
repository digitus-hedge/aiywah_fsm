<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'project_name',
        'project_code',
        'site_name',
        'site_address',
        'completion_date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}