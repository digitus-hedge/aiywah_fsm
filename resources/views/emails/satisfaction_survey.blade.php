<!DOCTYPE html>
<html>
<body style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;line-height:1.6;">

    <p>Dear {{ $customerName }},</p>

    <p>We hope you are satisfied with the maintenance service recently completed
       by our team.</p>

    <p style="margin-bottom:4px;"><strong>Project Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
        <tr><td style="color:#666;">Project</td><td>{{ $projectName }}</td></tr>
        <tr><td style="color:#666;">Location</td><td>{{ $location }}</td></tr>
    </table>

    <p style="margin-bottom:4px;"><strong>Service Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
        <tr><td style="color:#666;">Request Number</td><td><strong>{{ $reference }}</strong></td></tr>
        <tr><td style="color:#666;">Completion Date</td><td>{{ $completionDate }}</td></tr>
    </table>

    <p>At Aiywah FSM, we continuously strive to improve our quality of service,
       and your feedback plays an important role in helping us achieve that.</p>

    <p>We would appreciate it if you could take a minute to complete our short
       Customer Satisfaction Survey:</p>

    <p style="margin:20px 0;">
        <a href="{{ $surveyUrl }}"
           style="background:#0f766e;color:#ffffff;text-decoration:none;
                  padding:12px 22px;border-radius:6px;display:inline-block;
                  font-weight:bold;">
            Take the Survey
        </a>
    </p>

    <p style="font-size:12px;color:#777;">
        If the button does not work, copy and paste this link into your browser:<br>
        <span style="word-break:break-all;">{{ $surveyUrl }}</span>
    </p>

    <p>Your feedback helps us improve our service quality and better serve you
       in the future.</p>

    <p>Thank you once again for choosing Aiywah FSM. We sincerely appreciate your
       trust and look forward to serving you again.</p>

    <p>
        Kind Regards,<br>
        <strong>Aiywah FSM</strong><br>
        Post-Handover Maintenance Team
    </p>

</body>
</html>