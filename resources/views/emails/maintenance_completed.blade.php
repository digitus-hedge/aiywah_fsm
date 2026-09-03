<!DOCTYPE html>
<html>
<body style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;line-height:1.6;">

    <p>Dear {{ $customerName }},</p>

    <p>We are pleased to inform you that the maintenance work for your service
       request has been successfully completed.</p>

    <p style="margin-bottom:4px;"><strong>Project Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
        <tr><td style="color:#666;">Project</td><td>{{ $projectName }}</td></tr>
        <tr><td style="color:#666;">Location</td><td>{{ $location }}</td></tr>
    </table>

    <p style="margin-bottom:4px;"><strong>Request Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
        <tr><td style="color:#666;">Request Number</td><td><strong>{{ $reference }}</strong></td></tr>
        <tr><td style="color:#666;vertical-align:top;">Issue Reported</td><td>{{ $issue }}</td></tr>
    </table>

    <p style="margin-bottom:4px;"><strong>Completion Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;">
        <tr><td style="color:#666;">Technician</td><td>{{ $technician }}</td></tr>
        <tr><td style="color:#666;">Completion Date</td><td>{{ $completionDate }}</td></tr>
        <tr><td style="color:#666;">Completion Time</td><td>{{ $completionTime }}</td></tr>
    </table>

    <p>You can view the complete record of this service request online - including
       the before &amp; after photos, technician notes and the signed work completion
       report:</p>

    <p style="margin:20px 0;">
        <a href="{{ $portalUrl }}"
           style="background:#0f766e;color:#ffffff;text-decoration:none;
                  padding:12px 22px;border-radius:6px;display:inline-block;
                  font-weight:bold;">
            View Service Request Details
        </a>
    </p>

    <p style="font-size:12px;color:#777;">
        If the button does not work, copy and paste this link into your browser:<br>
        <span style="word-break:break-all;">{{ $portalUrl }}</span>
    </p>

    <p>If you have any questions or require any further assistance, please feel free
       to contact our Service Team.</p>

    <p>Thank you for your continued trust in Matter Mind. It has been our pleasure
       to assist you.</p>

    <p>
        Kind Regards,<br>
        <strong>Matter Mind</strong><br>
        Post-Handover Maintenance Team
    </p>

</body>
</html>