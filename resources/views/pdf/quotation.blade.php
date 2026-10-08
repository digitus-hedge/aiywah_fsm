<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Quotation {{ $quote['ref'] }}</title>
<style>
  @page { margin: 16mm 15mm 20mm 15mm; }
  body { font-family: "DejaVu Sans", sans-serif; font-size: 10px; color: #1f2430; line-height: 1.45; margin: 0; }
  table { width: 100%; border-collapse: collapse; }
  td, th { vertical-align: top; }

  .brand { font-size: 17px; font-weight: bold; color: #7a6140; }
  .brand-sub { font-size: 9px; color: #6b7180; margin-top: 2px; }
  .doc-title { font-size: 20px; font-weight: bold; letter-spacing: 2px; color: #9a7b4f; text-align: right; }
  .meta { text-align: right; font-size: 9.5px; color: #555b6b; margin-top: 4px; }
  .meta strong { color: #1f2430; }
  .rule { border-top: 2px solid #9a7b4f; margin: 10px 0 14px; }

  .lbl { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #8a8f9c; margin-bottom: 3px; }
  .box { background: #faf7f2; border: 1px solid #eadfcd; padding: 9px 11px; }
  .box strong { font-size: 11px; }

  .items { margin-top: 14px; }
  .items th { background: #9a7b4f; color: #ffffff; font-size: 8.5px; text-transform: uppercase; letter-spacing: .6px; padding: 7px 8px; text-align: left; }
  .items td { padding: 7px 8px; border-bottom: 1px solid #e6e8ee; }
  .items th.r { text-align: right; }
  .r { text-align: right; }
  .c { text-align: center; }
  .nowrap { white-space: nowrap; }

  .totals { width: 46%; margin-left: 54%; margin-top: 10px; }
  .totals td { padding: 5px 8px; }
  .totals .grand td { border-top: 2px solid #9a7b4f; font-size: 12px; font-weight: bold; color: #7a6140; padding-top: 8px; }

  .notes { margin-top: 18px; page-break-inside: avoid; }
  .sign { margin-top: 26px; font-size: 9.5px; color: #555b6b; }
  .footer { position: fixed; left: 0; right: 0; bottom: -12mm; text-align: center; font-size: 8px; color: #8a8f9c; }
</style>
</head>
<body>

  <div class="footer">
    {{ config('app.name') }} · Quotation {{ $quote['ref'] }} · This is a computer-generated document.
  </div>



  {{-- Header --}}
  <table>
    <tr>

      <td style="width:55%;">
        <div class="brand">FSM Aiywah</div>
        <div class="brand-sub">Out-of-Warranty Service Quotation</div>
      </td>

    

      <td style="width:45%;">
        <div class="doc-title">QUOTATION</div>
        <div class="meta">
          Quotation No: <strong>{{ $quote['ref'] }}</strong><br>
          Date: <strong>{{ $quote['date']->format('d M Y') }}</strong><br>
          @if ($quote['expiry'])
            Valid Until: <strong>{{ $quote['expiry']->format('d M Y') }}</strong>
          @endif
        </div>
      </td>
    </tr>
  </table>

  <div class="rule"></div>

  {{-- Customer + service request --}}
  <table>
    <tr>
      <td style="width:50%; padding-right:6px;">
        <div class="box">
          <div class="lbl">Quotation For</div>
          <strong>{{ $quote['customer'] ?: '-' }}</strong><br>
          {{ $quote['site'] }}<br>
       {{ $quote['contact_name'] ?? '' }}<br>
  {{ $quote['contact_number'] ?? '' }}<br>

    {{ $quote['email'] ?? '' }}
        </div>
      </td>
      <td style="width:50%; padding-left:6px;">
        <div class="box">
          <div class="lbl">Service Request</div>
          <strong>{{ $quote['sr_number'] ?: '-' }}</strong><br>
          Scope: Out of Warranty<br/>

            Priority: {{ $quote['priority_level'] ?: '-' }}<br>
  Project: {{ $quote['project_name'] ?: '-' }}<br>
  Category: {{ $quote['category_name'] ?: '-' }}

        </div>
      </td>
    </tr>
  </table>

  {{-- Work quoted + amount --}}
  <table class="items">
    <thead>
      <tr>
        <th style="width:76%;">Description</th>
        <th class="r" style="width:24%;">Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{!! nl2br(e($quote['summary'])) !!}</td>
        <td class="r nowrap">{{ $money($quote['amount']) }}</td>
      </tr>
    </tbody>
  </table>

  {{-- Totals --}}
  <table class="totals">
    @if ($quote['discount'] > 0)
      <tr>
        <td>
          Discount
          @if ($quote['discount_type'] === 'percent')
            ({{ $pct($quote['discount_value']) }}%)
          @endif
        </td>
        <td class="r nowrap">- {{ $money($quote['discount']) }}</td>
      </tr>
    @endif
    @if ($quote['adjustment'] != 0)
      <tr>
        <td>Adjustment</td>
        <td class="r nowrap">{{ $money($quote['adjustment']) }}</td>
      </tr>
    @endif
    <tr class="grand">
      <td>Grand Total</td>
      <td class="r nowrap">{{ $money($quote['grand_total']) }}</td>
    </tr>
  </table>

  {{-- Notes / terms --}}
  @if ($quote['notes'] !== '')
    <div class="notes">
      <div class="lbl">Notes &amp; Terms</div>
      {!! nl2br(e($quote['notes'])) !!}
    </div>
  @endif

  @if ($quote['prepared_by'] !== '')
    <div class="sign">Prepared by: {{ $quote['prepared_by'] }}</div>
  @endif

</body>
</html>