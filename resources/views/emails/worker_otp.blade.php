<div style="font-family:-apple-system,'Segoe UI',Roboto,sans-serif;max-width:480px;margin:0 auto;padding:32px 24px;">
  <div style="text-align:center;margin-bottom:28px;">
    <div style="display:inline-block;width:56px;height:56px;line-height:56px;border-radius:14px;
                background:linear-gradient(135deg,#9A7B4F,#C4A882);color:#fff;font-size:22px;font-weight:700;">MM</div>
    <div style="font-size:16px;font-weight:700;color:#1f2937;margin-top:12px;">MATTER MIND</div>
    <div style="font-size:12px;color:#6b7280;">Technician Portal</div>
  </div>

  <p style="font-size:14px;color:#374151;line-height:1.6;">
    Use this code to verify your identity and set a new password:
  </p>

  <div style="text-align:center;margin:26px 0;">
    <div style="display:inline-block;background:#faf7f2;border:1.5px solid #e8dcc8;border-radius:12px;
                padding:18px 32px;font-size:32px;font-weight:700;letter-spacing:.28em;color:#9A7B4F;">
      {{ $otp }}
    </div>
  </div>

  <p style="font-size:13px;color:#6b7280;line-height:1.6;">
    This code expires in {{ $ttl }} minutes. If you did not request it, ignore this email
    and tell your supervisor.
  </p>

  <hr style="border:none;border-top:1px solid #f3f4f6;margin:24px 0;">
  <p style="font-size:11px;color:#9ca3af;text-align:center;">Never share this code with anyone.</p>
</div>