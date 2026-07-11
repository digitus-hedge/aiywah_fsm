<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Punchitem extends Model
{

    protected $table = 'punch_items';   // point to the real table

    protected $fillable = [ 'punch_id', 'name', 'category', 'qty', 'rate', 'line_total', 'receipt_path',
    'recon_status', 'reconciled_by', 'reconciled_at',];

    protected $casts = [
        'qty'           => 'decimal:2',
        'rate'          => 'decimal:2',
        'line_total'    => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    public function punch(): BelongsTo
    {
        return $this->belongsTo(Punch::class);
    }
    // 
    public function getReceiptUrlAttribute(): ?string
{
    return $this->receipt_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->receipt_path)
        : null;
}
}