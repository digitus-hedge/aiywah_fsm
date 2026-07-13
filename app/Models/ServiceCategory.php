<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use SoftDeletes;

    protected $table = 'service_categories';

    protected $fillable = ['category_name', 'description', 'color_code','icon', 'sort_order', 'status',
                             'created_by', 'updated_by',];

    protected $casts = ['status'     => 'boolean','sort_order' => 'integer',];

    public function domains(): HasMany
    {
        return $this->hasMany(ServiceDomain::class, 'service_category_id');
    }
}
