<!DOCTYPE html>
<html>
<body style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;line-height:1.6;">
    <p>Dear {{ $customerName }},</p>

    <p>Thank you for contacting <strong>Aiywah FSM Decor &amp; General Maintenance LLC</strong>.</p>

    <p>We have received your maintenance request and our team has started reviewing it.</p>

    <p><strong>Here are the details of the request:</strong></p>

    <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td style="color:#666;">Service Request No</td>
            <td><strong>{{ $reference }}</strong></td>
        </tr>
        <tr>
            <td style="color:#666;">Project</td>
            <td>{{ $projectName }}</td>
        </tr>
        <tr>
            <td style="color:#666;">Location</td>
            <td>{{ $location }}</td>
        </tr>
        <tr>
            <td style="color:#666;">Priority</td>
            <td>{{ $sr->priority_level }}</td>
        </tr>
        <tr>
            <td style="color:#666;">Reported By</td>
            <td>{{ $sr->reported_by }}</td>
        </tr>
    </table>

    <p>Our service coordinator will update you shortly with the next steps.</p>

    <p>
        Regards,<br>
        On behalf of Aiywah FSM Decor &amp; General Maintenance LLC
    </p>
</body>
</html>