<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PunchItem extends Model
{
    protected $fillable = ['punch_id', 'name', 'qty', 'rate', 'line_total'];

    protected $casts = [
        'qty'        => 'decimal:2',
        'rate'       => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function punch(): BelongsTo
    {
        return $this->belongsTo(Punch::class);
    }
}