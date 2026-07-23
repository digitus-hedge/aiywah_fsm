<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;
class ServiceDomain extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'service_category_id', 'domain_name', 'description',
        'sort_order', 'status', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_service_domain')
            ->withPivot('service_category_id')
            ->withTimestamps();
    }
}
