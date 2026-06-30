<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'client_id',
        'project_site',
        'service_type_id',
        'reported_by',
        'priority_level',
        'issue_description',
        'internal_remark',
        'status',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}