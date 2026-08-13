{{--
    resources/views/emails/client_welcome.blade.php

    Standalone HTML — deliberately not @component('mail::message'), so the
    layout carries Matter Mind branding rather than Laravel's default chrome.
    Table-based with inline styles: Outlook and Gmail strip <style> blocks.

    Expects: $client, $project (nullable), $portalUrl (nullable)
--}}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="x-apple-disable-message-reformatting"/>
  <title>Welcome to the Matter Mind Post-Handover Maintenance Portal</title>
  <!--[if mso]>
  <style>table,td,div,p,a{font-family:Arial,Helvetica,sans-serif !important;}</style>
  <![endif]-->
</head>
<body style="margin:0;padding:0;background:#f4f2ee;">

{{-- Preheader: the grey preview line in the inbox. Hidden in the body. --}}
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f4f2ee;">
  Your organization is now onboarded for post-handover maintenance and service management.
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f2ee;">
<tr>
<td align="center" style="padding:28px 12px;">

  <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
         style="width:600px;max-width:100%;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e8e3da;">

    {{-- ══ HEADER ══ --}}
    <tr>
      <td style="background:#9A7B4F;padding:26px 32px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="font-family:Georgia,'Times New Roman',serif;font-size:19px;font-weight:bold;color:#ffffff;letter-spacing:.3px;line-height:1.3;">
              Matter Mind
            </td>
            <td align="right" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#f0e7da;">
              Service That Matters. Always.
            </td>
          </tr>
        </table>
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#f0e7da;padding-top:8px;">
          Post-Handover Maintenance &amp; Service Management
        </div>
      </td>
    </tr>

    {{-- ══ BODY ══ --}}
    <tr>
      <td style="padding:32px 32px 8px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65;color:#33302b;">

        <h1 style="margin:0 0 18px;font-family:Georgia,'Times New Roman',serif;font-size:21px;font-weight:normal;color:#1f1d1a;line-height:1.35;">
          Welcome to the Post-Handover Maintenance Portal
        </h1>

        <p style="margin:0 0 14px;">Dear {{ $client->contact_name ?: 'Customer' }},</p>

        <p style="margin:0 0 14px;">Thank you for choosing Matter Mind.</p>

        <p style="margin:0 0 22px;">
          We are pleased to inform you that your organization has been successfully
          onboarded to our Post-Handover Maintenance &amp; Service Management System.
        </p>

      </td>
    </tr>

    {{-- ══ PROJECT DETAILS ══ --}}
    @if ($project)
    <tr>
      <td style="padding:0 32px 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="background:#faf8f4;border:1px solid #ebe4d8;border-radius:8px;">
          <tr>
            <td style="padding:16px 20px 6px;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;
                       letter-spacing:.08em;text-transform:uppercase;color:#8a7f6d;">
              Project Details
            </td>
          </tr>
          <tr>
            <td style="padding:0 20px 16px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                     style="font-family:Arial,Helvetica,sans-serif;font-size:13.5px;color:#33302b;">
                <tr>
                  <td width="46%" style="padding:7px 0;color:#7d7466;border-bottom:1px solid #ede7dc;">Project</td>
                  <td style="padding:7px 0;font-weight:bold;border-bottom:1px solid #ede7dc;">{{ $project->project_name ?: 'N/A' }}</td>
                </tr>
                <tr>
                  <td style="padding:7px 0;color:#7d7466;border-bottom:1px solid #ede7dc;">Location</td>
                  <td style="padding:7px 0;font-weight:bold;border-bottom:1px solid #ede7dc;">{{ $project->site_name ?: 'N/A' }}</td>
                </tr>
                <tr>
                  <td style="padding:7px 0;color:#7d7466;border-bottom:1px solid #ede7dc;">Shop Opening / Handover</td>
                  <td style="padding:7px 0;font-weight:bold;border-bottom:1px solid #ede7dc;">
                    {{ optional($project->completion_date)->format('d M Y') ?? 'To be confirmed' }}
                  </td>
                </tr>
                <tr>
                  <td style="padding:7px 0;color:#7d7466;">Warranty Expiry</td>
                  <td style="padding:7px 0;font-weight:bold;color:#8a5a2a;">
                    {{ optional($project->warranty_end_date)->format('d M Y') ?? 'To be confirmed' }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    @endif

    {{-- ══ WHAT TO EXPECT ══ --}}
    <tr>
      <td style="padding:0 32px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65;color:#33302b;">

        <p style="margin:0 0 20px;">
          Through this platform, we will manage all future maintenance and service
          requests in a structured and transparent manner.
        </p>

        <p style="margin:0 0 12px;font-family:Georgia,'Times New Roman',serif;font-size:16px;color:#1f1d1a;">
          What you can expect
        </p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="font-family:Arial,Helvetica,sans-serif;font-size:13.5px;line-height:1.55;color:#33302b;">
          @foreach ([
            'A dedicated Service Request number for every request',
            'WhatsApp and email updates throughout the service process',
            'Engineer visit scheduling and notifications',
            'Status updates from request creation until completion',
            'Service history and maintenance records',
            'Faster response and improved communication',
            'A clear distinction between warranty and post-warranty services',
          ] as $item)
          <tr>
            <td width="20" valign="top" style="padding:4px 0;color:#9A7B4F;font-weight:bold;">&#8250;</td>
            <td style="padding:4px 0;">{{ $item }}</td>
          </tr>
          @endforeach
        </table>

      </td>
    </tr>

    {{-- ══ CTA ══ --}}
    @if ($portalUrl)
    <tr>
      <td align="center" style="padding:28px 32px 8px;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td align="center" style="background:#9A7B4F;border-radius:6px;">
              <a href="{{ $portalUrl }}"
                 style="display:inline-block;padding:13px 30px;font-family:Arial,Helvetica,sans-serif;
                        font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">
                View your projects
              </a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" style="padding:0 32px 8px;font-family:Arial,Helvetica,sans-serif;font-size:11.5px;color:#8a7f6d;">
        This link is private to your organization — please keep it safe.
      </td>
    </tr>
    <tr>
      <td style="padding:10px 32px 0;font-family:Arial,Helvetica,sans-serif;font-size:11.5px;color:#8a7f6d;line-height:1.5;">
        If the button does not work, copy this link into your browser:<br/>
        <span style="word-break:break-all;color:#9A7B4F;">{{ $portalUrl }}</span>
      </td>
    </tr>
    @endif

    {{-- ══ CLOSING ══ --}}
    <tr>
      <td style="padding:24px 32px 30px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65;color:#33302b;">

        <p style="margin:0 0 14px;">
          If your organization would like additional members of your team to receive
          service updates, please let our team know and we will add them to the
          notification list.
        </p>

        <p style="margin:0 0 22px;">
          We look forward to supporting you and providing the highest level of
          after-sales service.
        </p>

        <p style="margin:0;color:#7d7466;">Kind regards,</p>
        <p style="margin:2px 0 0;font-weight:bold;color:#1f1d1a;">
          Matter Mind Decor &amp; General Maintenance LLC
        </p>
        <p style="margin:1px 0 0;color:#7d7466;font-size:13px;">Post-Handover Maintenance Team</p>

      </td>
    </tr>

    {{-- ══ FOOTER ══ --}}
    <tr>
      <td style="background:#faf8f4;border-top:1px solid #ebe4d8;padding:18px 32px;
                 font-family:Arial,Helvetica,sans-serif;font-size:11px;line-height:1.6;color:#98907f;">
        You are receiving this because your organization was onboarded to the
        Matter Mind service portal.<br/>
        &copy; {{ date('Y') }} Matter Mind Decor &amp; General Maintenance LLC
      </td>
    </tr>

  </table>

</td>
</tr>
</table>

</body>
</html>