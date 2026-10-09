<?php

namespace App\Http\Controllers\Concerns;

use App\Jobs\SendSrNotifications;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Quotation;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\PdfTemplate;
/**
 * Invoice Panel: builds an invoice from the Create Invoice form, renders the
 * PDF, and stores everything in the `invoices` table.
 *
 * Used by ServiceRequestController next to BuildsQuotationPdf. It relies on
 * two helpers that already exist in that controller: buildSrRef() and
 * srForQuote(). hopApprove() in the controller calls buildInvoiceMail()
 * and sendInvoiceMail() from here.
 */
trait BuildsInvoicePdf
{
    /* ============================================================
     |  OPTIONS SHARED WITH THE VIEW
     * ============================================================ */

    /** Currency shown on the form and the PDF. Change it here only. */
    public static function invoiceCurrency(): array
    {
        return ['code' => 'AED', 'symbol' => 'AED'];
    }

    /** key => [label, days until due]. `null` days = the user picks the due date. */
    public static function invoicePaymentTerms(): array
    {
        return [
            'net_30' => ['label' => 'Pay within 30 days', 'days' => 30],
            'custom' => ['label' => 'Custom',             'days' => null],
        ];
    }

    /**
     * Invoice figures: the amount (the quotation total) plus any additional
     * amount. The browser shows the same sum while typing, but only these
     * server-side numbers are saved and printed.
     */
    public static function invoiceTotals($amount, $additional = 0): array
    {
        $amount     = round(max(0, (float) $amount), 2);
        $additional = round(max(0, (float) $additional), 2);

        return [
            'sub_total'         => $amount,
            'additional_amount' => $additional,
            'grand_total'       => round($amount + $additional, 2),
        ];
    }

    /* ============================================================
     |  ROUTE ACTIONS
     * ============================================================ */

    /** Renders the PDF from the form without saving anything. */
    public function invoicePreview(Request $request, ServiceRequest $serviceRequest)
    {
        $inv = $this->buildInvoice($request, $serviceRequest);

        return $this->invoicePdf($inv)->stream('invoice-preview.pdf');
    }

    /**
     * Saves the invoice, generates its PDF and moves the SR on to
     * "Invoice Submitted" (the HoP approval list).
     */
    public function invoiceGenerate(Request $request, ServiceRequest $serviceRequest)
    {
        $inv       = $this->buildInvoice($request, $serviceRequest);
        $oldStatus = $serviceRequest->status;
        $path      = null;

        try {
            $invoice = DB::transaction(function () use ($inv, $serviceRequest, &$path) {
                // Lock the SR so a double click cannot raise two invoices.
                $sr = ServiceRequest::whereKey($serviceRequest->id)->lockForUpdate()->first();

                if (!$sr || $sr->status !== 'Pending Invoice') {
                    throw new HttpResponseException(response()->json([
                        'ok'      => false,
                        'message' => 'This service request is no longer awaiting an invoice. Refresh the page.',
                    ], 422));
                }

                $invoice = Invoice::create([
                    'service_request_id' => $sr->id,
                    'quotation_id'       => $inv['quotation_id'],
                    'invoice_date'       => $inv['invoice_date'],
                    'payment_terms'      => $inv['payment_terms'],
                    'due_date'           => $inv['due_date'],
                    'items'              => $inv['items'],
                    'sub_total'          => $inv['sub_total'],
                    'additional_amount'  => $inv['additional_amount'],
                    'additional_note'    => $inv['additional_note'],
                    'grand_total'        => $inv['grand_total'],
                    'currency'           => $inv['currency'],
                    'notes'              => $inv['notes'],
                    'created_by'         => Auth::id(),
                ]);

                // Invoice number comes from the row id, so it can never repeat.
                $inv['invoice_no'] = 'INV-' . Carbon::parse($inv['invoice_date'])->year
                    . '-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT);

                // Random suffix: the file is on the public disk, so the URL must not be guessable.
                $path = 'invoices/' . $inv['invoice_no'] . '-' . Str::lower(Str::random(16)) . '.pdf';
                Storage::disk('public')->put($path, $this->invoicePdf($inv)->output());

                $invoice->update([
                    'invoice_no' => $inv['invoice_no'],
                    'pdf_path'   => $path,
                ]);

                // The SR keeps a copy of the headline figures - the HoP list,
                // the kanban and the monthly totals already read these columns.
                $sr->update([
                    'status'               => 'Invoice Submitted',
                    'invoice_code'         => $inv['invoice_no'],
                    'invoice_total'        => $inv['grand_total'],
                    'invoice_path'         => $path,
                    'invoice_submitted_at' => now(),
                    'invoice_uploaded_by'  => Auth::id(),
                ]);

                return $invoice;
            });
        } catch (HttpResponseException | ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // The transaction rolled back; remove the PDF if it was already written.
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Invoice generation failed', [
                'sr_id' => $serviceRequest->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'The invoice could not be generated, so nothing was changed.'
                    . (config('app.debug') ? ' Reason: ' . $e->getMessage() : ' Please try again.'),
            ], 500);
        }

        $ref    = $this->buildSrRef($serviceRequest);
        $symbol = static::invoiceCurrency()['symbol'];

        NotificationLog::create([
            'service_request_id' => $serviceRequest->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => $ref . ' invoice generated - ' . $invoice->invoice_no
                . ' (' . $symbol . number_format((float) $invoice->grand_total, 2) . ')',
            'from_status' => $oldStatus,
            'to_status'   => 'Invoice Submitted',
            'caused_by'   => Auth::id(),
        ]);

        SendSrNotifications::dispatch(
            $serviceRequest->id,
            SendSrNotifications::INVOICE_SUBMITTED,
            $ref,
            Auth::user()?->name
        );

        return response()->json([
            'ok'          => true,
            'message'     => 'Invoice generated. HoP notified.',
            'invoice_id'  => $invoice->id,
            'invoice_no'  => $invoice->invoice_no,
            'grand_total' => (float) $invoice->grand_total,
            'pdf_url'     => asset('storage/' . $path),
            'customer_email' => $inv['email'] ?? '',   // pre-fills the email in the HoP approval dialog
        ]);
    }

    /* ============================================================
     |  HELPERS
     * ============================================================ */

    /** Validates the Create Invoice form and returns everything the PDF and the table need. */
private function buildInvoice(Request $request, ServiceRequest $sr): array
{
    $terms = static::invoicePaymentTerms();

    $data = $request->validate([
        'invoice_date'      => ['required', 'date'],
        'payment_terms'     => ['required', Rule::in(array_keys($terms))],
        'due_date'          => ['required', 'date', 'after_or_equal:invoice_date'],
        'amount'            => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
        'additional_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
        'additional_note'   => ['nullable', 'string', 'max:255'],
        'notes'             => ['nullable', 'string', 'max:2000'],
        'pdf_template_id'   => ['nullable', 'integer', Rule::exists('pdf_templates', 'id')->where('status', 1)],
    ]);

    $sr->loadMissing('category');
    $quotation = Quotation::where('service_request_id', $sr->id)->latest('id')->first();

    // The quotation total is the invoice amount. The typed amount is only
    // used for an SR that has no quotation amount saved.
    $quoted = $quotation ? (float) $quotation->grand_total : 0.0;
    $amount = $quoted > 0 ? $quoted : (float) ($data['amount'] ?? 0);

    if ($amount <= 0) {
        throw ValidationException::withMessages([
            'amount' => 'Enter the invoice amount.',
        ]);
    }

    $totals = static::invoiceTotals($amount, $data['additional_amount'] ?? 0);

    if ($totals['grand_total'] > 9999999999.99) {
        throw ValidationException::withMessages([
            'amount' => 'The invoice total is too large.',
        ]);
    }

    // One line on the invoice: what the work was, and the amount for it.
    $items = [[
        'name'        => optional($sr->category)->category_name ?: 'Service Charges',
        'description' => Str::limit(trim((string) ($quotation?->summary ?: $sr->issue_description)), 500) ?: null,
        'amount'      => $totals['sub_total'],
    ]];

    // Fixed terms decide the due date; only "Custom" takes the typed one.
    $invoiceDate = Carbon::parse($data['invoice_date'])->startOfDay();
    $days        = $terms[$data['payment_terms']]['days'];
    $dueDate     = $days === null
        ? Carbon::parse($data['due_date'])->startOfDay()
        : $invoiceDate->copy()->addDays($days);

    $currency = static::invoiceCurrency();

    // srForQuote() gives the customer / site / project block used on the quotation PDF.
    return array_merge($this->srForQuote($sr), $totals, [
        'items'               => $items,
        'invoice_no'          => 'DRAFT',
        'invoice_date'        => $invoiceDate->toDateString(),
        'due_date'            => $dueDate->toDateString(),
        'payment_terms'       => $data['payment_terms'],
        'payment_terms_label' => $terms[$data['payment_terms']]['label'],
        'created_by_name'     => Auth::user()?->name,   // whoever is creating the invoice
        'quotation_id'        => $quotation?->id,
        'quote_ref'           => $quotation?->quote_ref,
        'additional_note'     => trim((string) ($data['additional_note'] ?? '')) ?: null,
        'notes'               => trim((string) ($data['notes'] ?? '')) ?: null,
        'currency'            => $currency['code'],
        'currency_symbol'     => $currency['symbol'],
        'template'            => $this->invoiceTemplate($data['pdf_template_id'] ?? null),
    ]);
}
    

    /**
     * The one place that talks to the PDF library. This assumes
     * barryvdh/laravel-dompdf (any version); if quotePdf() in
     * BuildsQuotationPdf uses a different library, use the same call here.
     */
    private function invoicePdf(array $inv)
    {
        return app('dompdf.wrapper')
            ->loadView('pdf.invoice', ['inv' => $inv])
            ->setPaper('a4');
    }

    /** Header / watermark / footer images of the chosen PDF template, ready for the PDF view. */
private function invoiceTemplate($id): ?array
{
    $tpl = $id ? PdfTemplate::where('status', 1)->find($id) : null;
    if (! $tpl) {
        return null;
    }

    $image = function (string $field) use ($tpl) {
        $src = $tpl->dataUri($field);
        if (! $src) {
            return null;
        }
        $size = @getimagesize(Storage::disk('public')->path($tpl->{$field}));

        return [
            'src'   => $src,
            'ratio' => ($size && $size[0] > 0) ? $size[1] / $size[0] : 0.2,   // height ÷ width
        ];
    };

    return [
        'header'    => $image('header_image'),
        'watermark' => $image('letterhead_image'),
        'footer'    => $image('footer_image'),
    ];
}

    /* ============================================================
     |  EMAILING THE INVOICE (used by hopApprove)
     * ============================================================ */

    /**
     * Validates the email fields of the HoP approval dialog.
     * Returns null when the user chose not to email the invoice.
     */
    private function buildInvoiceMail(Request $request): ?array
    {
        if (!$request->boolean('send_email')) {
            return null;
        }

        $data = $request->validate([
            'email_to'      => ['required', 'string', 'max:1000'],
            'email_subject' => ['required', 'string', 'max:200'],
            'email_message' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'email_to'      => 'To address',
            'email_subject' => 'subject',
            'email_message' => 'message',
        ]);

        // "To" may hold several addresses separated by commas.
        $to = array_values(array_unique(
            preg_split('/[,;\s]+/', $data['email_to'], -1, PREG_SPLIT_NO_EMPTY)
        ));

        if (!$to || count($to) > 10) {
            throw ValidationException::withMessages([
                'email_to' => 'Enter between 1 and 10 email addresses.',
            ]);
        }
        foreach ($to as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    'email_to' => 'This email address is not valid: ' . $address,
                ]);
            }
        }

        return [
            'from'    => config('mail.from.address'),
            'to'      => $to,
            'subject' => $data['email_subject'],
            'message' => trim((string) ($data['email_message'] ?? '')),
        ];
    }

    /**
     * Emails the stored invoice PDF to the customer. Throws when the PDF is
     * missing or the mail cannot be sent, so the caller can stop before it
     * closes the service request.
     */
    private function sendInvoiceMail(ServiceRequest $sr, array $mail): void
    {
        $path = $sr->invoice_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            throw new \RuntimeException('The invoice PDF for this service request was not found.');
        }

        $invoice   = Invoice::where('service_request_id', $sr->id)->latest('id')->first();
        $invoiceNo = $invoice?->invoice_no ?: ($sr->invoice_code ?: 'Invoice');
        $symbol    = static::invoiceCurrency()['symbol'];
        $pdf       = Storage::disk('public')->get($path);

        Mail::send('emails.invoice_to_customer', [
            'body'      => $mail['message'],
            'invoiceNo' => $invoiceNo,
            'srRef'     => $this->buildSrRef($sr),
            'amount'    => $symbol . ' ' . number_format((float) ($invoice?->grand_total ?? $sr->invoice_total), 2),
            'dueDate'   => $invoice?->due_date?->format('d M Y'),
        ], function ($m) use ($mail, $pdf, $invoiceNo) {
            $m->from($mail['from'], config('mail.from.name'))
                ->to($mail['to'])
                ->subject($mail['subject'])
                ->attachData($pdf, $invoiceNo . '.pdf', ['mime' => 'application/pdf']);
        });
    }
}