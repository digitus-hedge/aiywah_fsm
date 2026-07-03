<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- Import the Trait

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
        'status'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}