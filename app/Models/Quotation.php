<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'service_request_id', 'quote_ref', 'quote_date', 'expiry_date',
        'summary', 'notes',
        'amount', 'discount_type', 'discount_value', 'discount_amount',
        'adjustment', 'grand_total',
        'pdf_path', 'sent_to', 'sent_cc', 'email_subject', 'created_by',
    ];

    protected $casts = [
        'quote_date'  => 'date',
        'expiry_date' => 'date',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
