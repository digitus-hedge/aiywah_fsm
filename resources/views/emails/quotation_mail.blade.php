{{--
  Email sent to the customer with the quotation.
  Save as: resources/views/emails/quotation_mail.blade.php

  Receives:
    $quote  - array built by BuildsQuotationPdf::buildQuote()
    $intro  - the message typed in the Send Quotation dialog
    $money  - formats a number as "₹ 1,23,456.00"

  Email programs ignore most CSS, so every style is written on the element
  itself and the layout uses tables.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Quotation {{ $quote['ref'] }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f1ec;font-family:Arial,Helvetica,sans-serif;color:#1f2430;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f1ec;">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #e6dfd2;border-radius:8px;">
          <tr>
            <td style="padding:24px 28px 8px 28px;font-size:15px;line-height:1.55;">
              @if ($intro !== '')
                {!! nl2br(e($intro)) !!}
              @endif
            </td>
          </tr>
          <tr>
            <td style="padding:12px 28px 4px 28px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e6dfd2;border-radius:8px;">
                <tr>
                  <td colspan="2" align="center" style="background:#faf7f2;padding:16px;border-radius:8px 8px 0 0;">
                    <div style="font-size:14px;font-weight:bold;color:#1f2430;">Quotation Amount</div>
                    <div style="font-size:22px;font-weight:bold;color:#7a6140;padding-top:4px;">{{ $money($quote['grand_total']) }}</div>
                  </td>
                </tr>
                <tr>
                  <td style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;color:#6b7180;">Quotation Number</td>
                  <td align="right" style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;font-weight:bold;">{{ $quote['ref'] }}</td>
                </tr>
                <tr>
                  <td style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;color:#6b7180;">Quotation Date</td>
                  <td align="right" style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;font-weight:bold;">{{ $quote['date']->format('d M Y') }}</td>
                </tr>
                @if ($quote['sr_number'] !== '')
                <tr>
                  <td style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;color:#6b7180;">Service Request</td>
                  <td align="right" style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;font-weight:bold;">{{ $quote['sr_number'] }}</td>
                </tr>
                @endif
                @if ($quote['expiry'])
                <tr>
                  <td style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;color:#6b7180;">Valid Until</td>
                  <td align="right" style="padding:10px 16px;border-top:1px solid #e6dfd2;font-size:13px;font-weight:bold;">{{ $quote['expiry']->format('d M Y') }}</td>
                </tr>
                @endif
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:14px 28px 24px 28px;font-size:13px;line-height:1.55;color:#6b7180;">
              <strong style="color:#1f2430;">Work quoted:</strong><br>
              {!! nl2br(e($quote['summary'])) !!}
            </td>
          </tr>
        </table>
        <div style="max-width:560px;padding:12px 4px 0 4px;font-size:11px;color:#8a8f9c;">
          Sent by {{ config('app.name') }}.
        </div>
      </td>
    </tr>
  </table>
</body>
</html>