<?php

namespace App\Http\Controllers\Concerns;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Builds the out-of-warranty quotation PDF on the server and emails it.
 *
 * Save as: app/Http/Controllers/Concerns/BuildsQuotationPdf.php
 * Needs:   composer require barryvdh/laravel-dompdf
 * Views:   resources/views/pdf/quotation.blade.php      (the PDF)
 *          resources/views/emails/quotation_mail.blade.php   (the email)
 *
 * Used by ServiceRequestController::quoteSubmit() and ::quotePreview().
 */
// hgy
trait BuildsQuotationPdf
{
    protected function quoteRules(): array
    {
        return [
            'summary'        => ['required', 'string', 'min:3', 'max:500'],
            'expiry_date'    => ['nullable', 'date', 'after_or_equal:today'],
            'erp_quote_ref'  => ['nullable', 'string', 'max:60', 'regex:/^[A-Za-z0-9_-]+$/'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'discount_type'  => ['required', 'in:percent,flat'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'adjustment'     => ['nullable', 'numeric'],
            'amount'         => ['required', 'numeric', 'gt:0', 'max:999999999.99'],
        ];
    }

    /**
     * Validate the request and work out the discount and grand total on the
     * server. Totals sent by the browser are never trusted.
     *
     * @param  array{sr_id:int|string, sr_number:string, customer:string, site:string}  $sr
     */
    protected function buildQuote(Request $request, array $sr): array
    {
        $v = $request->validate($this->quoteRules());

        // One amount for the whole job; discount and adjustment are applied to it.
        $sub = round((float) $v['amount'], 2);

        $discountType  = $v['discount_type'];
        $discountValue = (float) ($v['discount_value'] ?? 0);
        if ($discountType === 'percent' && $discountValue > 100) {
            throw ValidationException::withMessages([
                'discount_value' => 'Discount cannot be more than 100%.',
            ]);
        }
        $discount = $discountType === 'percent'
            ? round($sub * $discountValue / 100, 2)
            : min(round($discountValue, 2), $sub);

        $adjustment = round((float) ($v['adjustment'] ?? 0), 2);
        $grand      = round($sub - $discount + $adjustment, 2);
        if ($grand < 0) {
            throw ValidationException::withMessages([
                'adjustment' => 'Grand total cannot be negative.',
            ]);
        }

        // Use the ERP reference when one was typed, otherwise make one.
        $ref = strtoupper(trim($v['erp_quote_ref'] ?? ''));
        if ($ref === '') {
            $ref = 'QT-' . now()->format('Ymd') . '-' . $sr['sr_id'];
        }

        return [
            'ref'            => $ref,
            'date'           => now(),
            'expiry'         => ! empty($v['expiry_date']) ? \Illuminate\Support\Carbon::parse($v['expiry_date']) : null,
            'sr_number'      => $sr['sr_number'] ?? '',
            'customer'       => $sr['customer'] ?? '',
            'client_id'      => $sr['client_id'] ?? null,
            'contact_name'   => $sr['contact_person'] ?? '',
            'contact_number' => $sr['contact_number'] ?? '',
            'email'          => $sr['email'] ?? '',
            'priority_level' => $sr['priority_level'] ?? '',
            'project_name'   => $sr['project_name'] ?? '',
            'category_name' => $sr['category_name'] ?? '',
            'site'           => $sr['site'] ?? '',
            'summary'        => trim($v['summary']),
            'notes'          => trim($v['notes'] ?? ''),
            'amount'         => $sub,
            'discount_type'  => $discountType,
            'discount_value' => $discountValue,
            'discount'       => $discount,
            'adjustment'     => $adjustment,
            'grand_total'    => $grand,
            'prepared_by'    => optional($request->user())->name ?? '',
        ];
    }

    /** The PDF object - call ->output() for the bytes or ->stream() to show it. */
    
    protected function quotePdf(array $quote)
    {
        // return Pdf::loadView('pdf.quotation', [
        //     'quote' => $quote,
       
        // 'template' => \App\Models\PdfTemplate::forType('quotation'),
        //     'money' => fn ($n) => '₹ ' . $this->inr((float) $n),
        //     'pct'   => fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.'),
        // ])->setPaper('a4');


        $template = \App\Models\PdfTemplate::forClient($quote['client_id'] ?? null);

        return Pdf::loadView('pdf.quotation', [
        'quote' => $quote,
        'tpl'   => [
        'header'     => $template?->dataUri('header_image'),
        'letterhead' => $template?->dataUri('letterhead_image'),
        'footer'     => $template?->dataUri('footer_image'),
        ],
        'money' => fn ($n) => '₹ ' . $this->inr((float) $n),
        'pct'   => fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.'),
        ])->setPaper('a4');

    }




    /**
     * Store the PDF and return the stored path. Pass $bytes when the PDF has
     * already been rendered, so it is not built twice.
     */
    protected function saveQuotePdf(array $quote, string $disk = 'public', ?string $bytes = null): string
    {
        $path = 'quotations/' . $quote['ref'] . '-' . now()->format('YmdHis') . '.pdf';
        Storage::disk($disk)->put($path, $bytes ?? $this->quotePdf($quote)->output());

        return $path;
    }

    /* ------------------------------------------------------------
     |  Email
     * ------------------------------------------------------------ */

    protected function quoteMailRules(): array
    {
        return [
            'email_to'      => ['required', 'string', 'max:500'],
            'email_cc'      => ['nullable', 'string', 'max:500'],
            'email_bcc'     => ['nullable', 'string', 'max:500'],
            'email_subject' => ['required', 'string', 'max:200'],
            'email_message' => ['nullable', 'string', 'max:5000'],
            'attach_pdf'    => ['nullable', 'boolean'],
        ];
    }

    /** Validate the Send dialog's fields: who it goes to, the subject and the message. */
    protected function buildQuoteMail(Request $request): array
    {
        $v = $request->validate($this->quoteMailRules(), [], [
            'email_to'      => 'To address',
            'email_cc'      => 'CC',
            'email_bcc'     => 'BCC',
            'email_subject' => 'subject',
            'email_message' => 'message',
        ]);

        $to = $this->emailList($v['email_to'] ?? '', 'email_to');
        if (! $to) {
            throw ValidationException::withMessages([
                'email_to' => "Enter the customer's email address.",
            ]);
        }

        return [
            'to'      => $to,
            'cc'      => $this->emailList($v['email_cc'] ?? '', 'email_cc'),
            'bcc'     => $this->emailList($v['email_bcc'] ?? '', 'email_bcc'),
            'subject' => trim(preg_replace('/\s+/', ' ', $v['email_subject'])),   // one line only
            'message' => trim($v['email_message'] ?? ''),
            'attach'  => (bool) ($v['attach_pdf'] ?? true),
        ];
    }

    /** "a@x.com, b@y.com" -> ['a@x.com', 'b@y.com'], rejecting anything that is not an email address. */
    protected function emailList(?string $raw, string $field): array
    {
        $list = array_values(array_unique(array_filter(preg_split('/[,;\s]+/', (string) $raw))));

        if (count($list) > 10) {
            throw ValidationException::withMessages([$field => 'Use at most 10 email addresses.']);
        }
        foreach ($list as $address) {
            if (! filter_var($address, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([$field => "\"{$address}\" is not a valid email address."]);
            }
        }

        return $list;
    }

    /**
     * Email the quotation. Throws if the mail server refuses it, so the caller
     * can stop before marking the SR as quoted.
     */
    protected function sendQuoteMail(array $quote, array $mail, string $pdfBytes): void
    {
        Mail::send('emails.quotation_mail', [
            'quote' => $quote,
            'intro' => $mail['message'],
            'money' => fn ($n) => '₹ ' . $this->inr((float) $n),
        ], function ($m) use ($quote, $mail, $pdfBytes) {
            $m->to($mail['to'])->subject($mail['subject']);
            if ($mail['cc']) {
                $m->cc($mail['cc']);
            }
            if ($mail['bcc']) {
                $m->bcc($mail['bcc']);
            }
            if ($mail['attach']) {
                $m->attachData($pdfBytes, 'Quotation-' . $quote['ref'] . '.pdf', ['mime' => 'application/pdf']);
            }
        });
    }

    /** Indian digit grouping: 1234567.5 -> 12,34,567.50 */
    protected function inr(float $n): string
    {
        $neg         = $n < 0;
        [$int, $dec] = explode('.', number_format(abs($n), 2, '.', ''));
        if (strlen($int) > 3) {
            $last3 = substr($int, -3);
            $rest  = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($int, 0, -3));
            $int   = $rest . ',' . $last3;
        }

        return ($neg ? '-' : '') . $int . '.' . $dec;
    }
}