<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'service_request_id',
        'quotation_id',
        'invoice_no',
        'invoice_date',
        'payment_terms',
        'due_date',
        'items',
        'sub_total',
        'additional_amount',
        'additional_note',
        'grand_total',
        'currency',
        'notes',
        'pdf_path',
        'created_by',
    ];

    protected $casts = [
        'invoice_date'      => 'date',
        'due_date'          => 'date',
        'items'             => 'array',
        'sub_total'         => 'decimal:2',
        'additional_amount' => 'decimal:2',
        'grand_total'       => 'decimal:2',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}