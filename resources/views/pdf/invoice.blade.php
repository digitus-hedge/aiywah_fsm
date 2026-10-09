{{--
    Invoice PDF. Rendered by BuildsInvoicePdf::invoicePdf() with one variable: $inv.
    Written for DomPDF: tables only (no flexbox / grid) and DejaVu Sans, which
    carries the rupee sign.
--}}
@php
    $sym   = $inv['currency_symbol'] ?? '';
    $money = fn ($n) => $sym . ' ' . number_format((float) $n, 2);
    $date  = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '-';
    $draft = ($inv['invoice_no'] ?? 'DRAFT') === 'DRAFT';

    // Chosen PDF template. All sizes are in mm; an A4 page is 210 x 297.
$tpl = $inv['template'] ?? null;
$hdr = $tpl['header']    ?? null;
$wmk = $tpl['watermark'] ?? null;
$ftr = $tpl['footer']    ?? null;

$side       = 10;     // left / right page margin
$wmkOpacity = 0.12;   // raise it if the watermark is too faint

// Fit an image to the full page width, but never taller than $maxH.
$fit = function ($img, $maxH) {
    $w = 210;
    $h = 210 * $img['ratio'];
    if ($h > $maxH) { $w = $maxH / $img['ratio']; $h = $maxH; }
    return ['w' => round($w, 2), 'h' => round($h, 2), 'left' => round((210 - $w) / 2, 2)];
};
$hBox = $hdr ? $fit($hdr, 45) : null;   // header: at most 45 mm tall
$fBox = $ftr ? $fit($ftr, 30) : null;   // footer: at most 30 mm tall

$mTop    = $hBox ? $hBox['h'] + 6  : 9;
$mBottom = $fBox ? $fBox['h'] + 10 : 14;

// Watermark: centred in the printable area, 120 mm wide at most.
$wBox = null;
if ($wmk) {
    $areaH = 297 - $mTop - $mBottom;
    $w = 120;
    $h = $w * $wmk['ratio'];
    if ($h > $areaH * 0.8) { $h = $areaH * 0.8; $w = $h / $wmk['ratio']; }
    $wBox = [
        'w'    => round($w, 2),
        'h'    => round($h, 2),
        'left' => round((210 - 2 * $side - $w) / 2, 2),
        'top'  => round(($areaH - $h) / 2, 2),
    ];
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Invoice {{ $inv['invoice_no'] }}</title>
<style>
    @page { margin: {{ $mTop }}mm {{ $side }}mm {{ $mBottom }}mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #1f2937; line-height: 1.45; }
    table { width: 100%; border-collapse: collapse; }
    td, th { vertical-align: top; }
    .gold { color: #7a6140; }
    .muted { color: #6b7280; }
    .right { text-align: right; }

    .brand { font-size: 17px; font-weight: bold; color: #7a6140; }
    .doc-title { font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #7a6140; text-align: right; }
    .doc-no { font-size: 11px; text-align: right; margin-top: 2px; }
    .rule { border-top: 2px solid #9a8053; margin: 12px 0 14px; }

    .lbl { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; padding-bottom: 3px; }
    .party-name { font-size: 12px; font-weight: bold; }
    .meta td { padding: 2px 0; }
    .meta .k { color: #6b7280; width: 46%; }
    .meta .v { font-weight: bold; text-align: right; }

    .items { margin-top: 16px; }
    .items th { background: #9a8053; color: #ffffff; font-size: 9px; text-transform: uppercase; letter-spacing: .5px; padding: 7px 8px; text-align: left; }
    .items td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
    .items .num { text-align: right; white-space: nowrap; }
    .item-name { font-weight: bold; }
    .item-desc { color: #6b7280; font-size: 9.5px; margin-top: 2px; }

    .totals { margin-top: 10px; }
    .totals td { padding: 5px 8px; }
    .totals .k { text-align: right; color: #6b7280; }
    .totals .v { text-align: right; white-space: nowrap; width: 130px; }
    .totals .grand td { border-top: 2px solid #9a8053; font-size: 12.5px; font-weight: bold; color: #7a6140; padding-top: 8px; }

    .notes { margin-top: 22px; padding: 10px 12px; background: #f9f7f3; border-left: 3px solid #9a8053; }
    .footer { position: fixed; bottom: -{{ $fBox ? 7 : 9 }}mm; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #9ca3af; }
    .draft { text-align: right; font-size: 10px; font-weight: bold; color: #b45309; margin-top: 3px; }
</style>
</head>
<body>
{{-- PDF TEMPLATE IMAGES (repeat on every page) --}}
@if ($hBox)
    <div style="position:fixed; top:-{{ $mTop }}mm; left:{{ $hBox['left'] - $side }}mm; width:{{ $hBox['w'] }}mm; height:{{ $hBox['h'] }}mm;">
        <img src="{{ $hdr['src'] }}" style="width:{{ $hBox['w'] }}mm; height:{{ $hBox['h'] }}mm;">
    </div>
@endif
@if ($wBox)
    <div style="position:fixed; top:{{ $wBox['top'] }}mm; left:{{ $wBox['left'] }}mm; width:{{ $wBox['w'] }}mm; height:{{ $wBox['h'] }}mm; z-index:-1;">
        <img src="{{ $wmk['src'] }}" style="width:{{ $wBox['w'] }}mm; height:{{ $wBox['h'] }}mm; opacity:{{ $wmkOpacity }};">
    </div>
@endif
@if ($fBox)
    <div style="position:fixed; bottom:-{{ $mBottom }}mm; left:{{ $fBox['left'] - $side }}mm; width:{{ $fBox['w'] }}mm; height:{{ $fBox['h'] }}mm;">
        <img src="{{ $ftr['src'] }}" style="width:{{ $fBox['w'] }}mm; height:{{ $fBox['h'] }}mm;">
    </div>
@endif
<div class="footer">
    {{ config('app.name') }} &nbsp;·&nbsp; Invoice {{ $inv['invoice_no'] }} &nbsp;·&nbsp; This is a computer-generated invoice.
</div>

{{-- HEADER --}}
<table>
    <tr>
        <td style="width:55%;">
            @if (! $hBox)
                <div class="brand">{{ config('app.name') }}</div>
                <div class="muted">{{ config('mail.from.address') }}</div>
            @endif
        </td>
        <td style="width:45%;">
            <div class="doc-title">INVOICE</div>
            <div class="doc-no"># {{ $inv['invoice_no'] }}</div>
            @if ($draft)
                <div class="draft">PREVIEW - NOT YET GENERATED</div>
            @endif
        </td>
    </tr>
</table>

<div class="rule"></div>

{{-- BILL TO + INVOICE DETAILS --}}
<table>
    <tr>
        <td style="width:54%; padding-right:20px;">
            <div class="lbl">Bill To</div>
            <div class="party-name">{{ $inv['customer'] ?: '-' }}</div>
            @if (!empty($inv['contact_person']))
                <div>{{ $inv['contact_person'] }}</div>
            @endif
            @if (!empty($inv['contact_number']))
                <div class="muted">{{ $inv['contact_number'] }}</div>
            @endif
            @if (!empty($inv['email']))
                <div class="muted">{{ $inv['email'] }}</div>
            @endif
            @if (!empty($inv['site']))
                <div style="margin-top:5px;"><span class="muted">Site:</span> {{ $inv['site'] }}</div>
            @endif
            @if (!empty($inv['project_name']))
                <div><span class="muted">Project:</span> {{ $inv['project_name'] }}</div>
            @endif
        </td>
        <td style="width:46%;">
            <table class="meta">
                <tr><td class="k">Invoice Date</td><td class="v">{{ $date($inv['invoice_date']) }}</td></tr>
                <tr><td class="k">Payment Terms</td><td class="v">{{ $inv['payment_terms_label'] }}</td></tr>
                <tr><td class="k">Due Date</td><td class="v">{{ $date($inv['due_date']) }}</td></tr>
                <tr><td class="k">Service Request</td><td class="v">{{ $inv['sr_number'] }}</td></tr>
                @if (!empty($inv['quote_ref']))
                    <tr><td class="k">Quotation Ref</td><td class="v">{{ $inv['quote_ref'] }}</td></tr>
                @endif
                @if (!empty($inv['created_by_name']))
                    <tr><td class="k">Created By</td><td class="v">{{ $inv['created_by_name'] }}</td></tr>
                @endif
            </table>
        </td>
    </tr>
</table>

{{-- WHAT IS BEING INVOICED --}}
<table class="items">
    <thead>
        <tr>
            <th>Description</th>
            <th class="num" style="width:130px;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($inv['items'] as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item['name'] }}</div>
                    @if (!empty($item['description']))
                        <div class="item-desc">{!! nl2br(e($item['description'])) !!}</div>
                    @endif
                </td>
                <td class="num">{{ $money($item['amount']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- TOTALS --}}
<table class="totals">
    <tr>
        <td class="k">{{ !empty($inv['quote_ref']) ? 'Quotation Amount' : 'Amount' }}</td>
        <td class="v">{{ $money($inv['sub_total']) }}</td>
    </tr>
    @if ((float) $inv['additional_amount'] > 0)
        <tr>
            <td class="k">Additional Amount{{ !empty($inv['additional_note']) ? ' (' . $inv['additional_note'] . ')' : '' }}</td>
            <td class="v">{{ $money($inv['additional_amount']) }}</td>
        </tr>
    @endif
    <tr class="grand">
        <td class="k" style="color:#7a6140;">Total ({{ $inv['currency'] }})</td>
        <td class="v">{{ $money($inv['grand_total']) }}</td>
    </tr>
</table>

@if (!empty($inv['notes']))
    <div class="notes">
        <div class="lbl">Notes / Terms</div>
        {!! nl2br(e($inv['notes'])) !!}
    </div>
@endif

</body>
</html>