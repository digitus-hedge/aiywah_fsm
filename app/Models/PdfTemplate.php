<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    protected $fillable = [
        'type', 'company_name', 'tagline', 'address',
        'phone', 'email', 'tax_no', 'footer_text',
    ];

    public static function forType(string $type): self
    {
        return static::firstOrNew(['type' => $type]);
    }
}
