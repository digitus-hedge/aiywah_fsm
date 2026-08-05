<!DOCTYPE html>
<html>
<body style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;line-height:1.6;">

    <p>Dear {{ $user->name }},</p>

    <p>An account has been created for you on the Matter Mind Service Portal.</p>

    <p style="margin-bottom:4px;"><strong>Your Login Details</strong></p>
    <table cellpadding="5" cellspacing="0" style="border-collapse:collapse;margin-bottom:16px;">
        <tr><td style="color:#666;">Email</td><td><strong>{{ $user->email }}</strong></td></tr>
        <tr><td style="color:#666;">Temporary Password</td><td><strong>{{ $plainPassword }}</strong></td></tr>
        <tr><td style="color:#666;">Role</td><td>{{ $roleName }}</td></tr>
    </table>

    <p style="margin:20px 0;">
        <a href="{{ $loginUrl }}"
           style="background:#0f766e;color:#ffffff;text-decoration:none;
                  padding:12px 22px;border-radius:6px;display:inline-block;
                  font-weight:bold;">
            Log In to the Portal
        </a>
    </p>

    <p style="font-size:12px;color:#777;">
        If the button does not work, copy and paste this link into your browser:<br>
        <span style="word-break:break-all;">{{ $loginUrl }}</span>
    </p>

    <p><strong>Please change your password after your first login.</strong>
       Do not share these credentials with anyone.</p>

    <p>
        Kind Regards,<br>
        Matter Mind Service Portal
    </p>

</body>
</html>