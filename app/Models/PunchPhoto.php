<?php
// app/Models/PunchPhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class PunchPhoto extends Model
{
    use LogsActivity;

    protected $fillable = ['punch_id', 'type', 'path', 'sort_order'];

    public function punch(): BelongsTo
    {
        return $this->belongsTo(Punch::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? asset('storage/' . $this->path) : null;
    }
}