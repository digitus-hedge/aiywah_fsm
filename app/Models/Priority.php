<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Priority extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'display_order', 'color', 'status'];

    protected $casts = [
        'status'        => 'boolean',
        'display_order' => 'integer',
    ];

    public function sla(): HasOne
    {
        return $this->hasOne(SlaMatrix::class, 'priority_id');
    }
}
