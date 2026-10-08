{{--
    Email sent to the customer when the Head of Projects approves the invoice.
    Sent by BuildsInvoicePdf::sendInvoiceMail(); the invoice PDF is attached.
    The typed message arrives as $body ($message is reserved by Laravel mail).
--}}
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"/></head>
<body style="margin:0;padding:24px;background:#f5f3ef;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
    <tr>
        <td style="padding:18px 24px;background:#7a6140;border-radius:8px 8px 0 0;color:#ffffff;font-size:16px;font-weight:bold;">
            {{ config('app.name') }}
        </td>
    </tr>
    <tr>
        <td style="padding:24px;font-size:14px;line-height:1.6;">
            {!! nl2br(e($body)) !!}
        </td>
    </tr>
    <tr>
        <td style="padding:0 24px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;">
                <tr>
                    <td colspan="2" style="padding:14px;text-align:center;background:#f9f7f3;border-radius:8px 8px 0 0;">
                        <div style="font-size:12px;color:#6b7280;">Invoice Amount</div>
                        <div style="font-size:20px;font-weight:bold;color:#7a6140;">{{ $amount }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 14px;border-top:1px solid #e5e7eb;font-size:13px;color:#6b7280;">Invoice No</td>
                    <td style="padding:8px 14px;border-top:1px solid #e5e7eb;font-size:13px;font-weight:bold;text-align:right;">{{ $invoiceNo }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 14px;border-top:1px solid #e5e7eb;font-size:13px;color:#6b7280;">Service Request</td>
                    <td style="padding:8px 14px;border-top:1px solid #e5e7eb;font-size:13px;font-weight:bold;text-align:right;">{{ $srRef }}</td>
                </tr>
                @if (!empty($dueDate))
                <tr>
                    <td style="padding:8px 14px;border-top:1px solid #e5e7eb;font-size:13px;color:#6b7280;">Due Date</td>
                    <td style="padding:8px 14px;border-top:1px solid #e5e7eb;font-size:13px;font-weight:bold;text-align:right;">{{ $dueDate }}</td>
                </tr>
                @endif
            </table>
            <p style="font-size:12px;color:#6b7280;margin:14px 0 0;">The invoice is attached to this email as a PDF.</p>
        </td>
    </tr>
</table>
</body>
</html>