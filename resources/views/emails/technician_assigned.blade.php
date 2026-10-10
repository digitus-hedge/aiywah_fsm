<!DOCTYPE html>
<html>
<body style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;line-height:1.6;">

    @if($forTechnician)
        <p>Dear {{ $technician }},</p>
        <p>You have been assigned to the following maintenance request.
           Please review the details and attend the site at the scheduled time.</p>
    @else
        <p>Dear {{ $customerName }},</p>
        <p>We are pleased to inform you that a technician has been assigned to
           your maintenance request.</p>
    @endif

    <p style="margin-bottom:4px;"><strong>Project Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
        <tr><td style="color:#666;">Project</td><td>{{ $projectName }}</td></tr>
        <tr><td style="color:#666;">Location</td><td>{{ $location }}</td></tr>
        @if($forTechnician)
            <tr><td style="color:#666;">Client</td><td>{{ $customerName }}</td></tr>
        @endif
    </table>

    <p style="margin-bottom:4px;"><strong>Request Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
        <tr><td style="color:#666;">Request Number</td><td><strong>{{ $reference }}</strong></td></tr>
        <tr><td style="color:#666;vertical-align:top;">Issue Reported</td><td>{{ $issue }}</td></tr>
    </table>

    <p style="margin-bottom:4px;"><strong>Scheduled Visit</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;">
        <tr><td style="color:#666;">Technician</td><td>{{ $technician }}</td></tr>
        <tr><td style="color:#666;">Phone Number</td><td>{{ $techPhone }}</td></tr>
        <tr><td style="color:#666;">Visit Date</td><td>{{ $visitDate }}</td></tr>
        <tr><td style="color:#666;">Visit Time</td><td>{{ $visitTime }}</td></tr>
    </table>

    @unless($forTechnician)
        <p>Our technician will attend your site at the scheduled date and time to
           carry out the required maintenance work.</p>

        <p>If the proposed schedule is not convenient, or if you need to coordinate
           access to the site, please let us know before the scheduled visit. You may
           also contact the assigned technician directly using the phone number
           provided above if required.</p>

        <p>Thank you for your continued trust in Aiywah FSM.</p>
    @else
        <p>Please contact the service coordinator if you are unable to attend at the
           scheduled time.</p>
    @endunless

    <p>
        Kind Regards,<br>
        Post-Handover Maintenance Team
    </p>

</body>
</html>