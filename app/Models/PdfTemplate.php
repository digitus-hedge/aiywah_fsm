<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PdfTemplate extends Model
{
    protected $fillable = [
        'client_id', 'header_image', 'letterhead_image', 'footer_image', 'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /** The active template for a company, or null when it has none. */
    public static function forClient($clientId): ?self
    {
        if (! $clientId) {
            return null;
        }

        return static::where('client_id', $clientId)->where('status', 1)->first();
    }

    /** The image as a base64 string, which is the safest way to give it to the PDF. */
    public function dataUri(string $field): ?string
    {
        $path = $this->{$field};
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
    }
}