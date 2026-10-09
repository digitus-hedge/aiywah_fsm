<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PdfTemplate extends Model
{
    protected $fillable = [
        'template_name', 'header_image', 'letterhead_image', 'footer_image', 'status',
    ];

    
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