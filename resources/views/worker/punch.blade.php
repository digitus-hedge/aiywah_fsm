@php
    // ---- Derived display values (all sourced from DB models) ----
    $isPunchedIn = $punch->exists && $punch->punch_in_at;
    $isSubmitted = $punch->exists && $punch->status === 'submitted';

    $currency = fn ($v) => '₹' . number_format((float) $v, 2);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Worker Punch In / Out — {{ $sr->ref }}</title>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.1/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* ---- Brand theme: warm bronze #9a8053 ---- */
            --primary:        #9a8053;
            --primary-dark:   #7c663f;
            --primary-darker: #5f4e30;
            --primary-light:  #f5f0e8;
            --primary-tint:   #ede4d5;
            --primary-glow:   rgba(154,128,83,0.28);

            --success: #4b7f52;  --success-light: #e3efe4;  --success-dark: #38623d;
            --warning: #c08a2d;  --warning-light: #f7edd6;
            --danger:  #b4432f;  --danger-light:  #f6e2dd;
            --info:    #6d6a5a;  --info-light:    #efece3;

            --gray-50:#faf9f6; --gray-100:#f3f1ea; --gray-200:#e7e3d8;
            --gray-300:#d6d0c1; --gray-400:#a9a291; --gray-500:#807a6a;
            --gray-600:#5f5a4d; --gray-700:#453f34; --gray-800:#2c2820; --gray-900:#1a1712;

            --body-bg:#f3f1ea; --card-bg:#ffffff; --border:#e7e3d8;

            --shadow-sm: 0 1px 2px 0 rgba(60,50,30,0.05);
            --shadow:    0 1px 3px 0 rgba(60,50,30,0.09), 0 1px 2px -1px rgba(60,50,30,0.05);
            --shadow-md: 0 4px 6px -1px rgba(60,50,30,0.10), 0 2px 4px -2px rgba(60,50,30,0.06);
            --shadow-lg: 0 10px 20px -4px rgba(60,50,30,0.12), 0 4px 8px -4px rgba(60,50,30,0.06);
            --shadow-xl: 0 24px 38px -10px rgba(60,50,30,0.22), 0 10px 14px -8px rgba(60,50,30,0.10);

            --radius-sm:10px; --radius:14px; --radius-lg:18px; --radius-xl:24px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.9rem;
            background: var(--body-bg);
            background-image:
                radial-gradient(at 0% 0%, rgba(154,128,83,0.12) 0px, transparent 55%),
                radial-gradient(at 100% 0%, rgba(124,102,63,0.09) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(95,78,48,0.06) 0px, transparent 55%);
            min-height: 100vh;
            color: var(--gray-800);
            margin: 0; padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ============ TOP BAR ============ */
        .topbar {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-brand { display:flex; align-items:center; gap:12px; text-decoration:none; color:var(--gray-900); }
        .topbar-brand .brand-mark {
            width:38px; height:38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            border-radius:11px; display:flex; align-items:center; justify-content:center;
            color:#fff; font-weight:700; font-size:14px;
            font-family:'Plus Jakarta Sans',sans-serif;
            box-shadow: 0 4px 14px var(--primary-glow);
        }
        .topbar-brand .brand-text { font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:0.95rem; line-height:1.1; }
        .topbar-brand .brand-text small { display:block; font-size:10px; color:var(--gray-500); font-weight:500; margin-top:3px; letter-spacing:0.06em; }

        .live-time {
            display:flex; align-items:center; gap:8px;
            background:#fff; padding:7px 14px; border-radius:999px;
            border:1px solid var(--border); font-size:0.8rem; font-weight:600;
            color:var(--gray-700); box-shadow:var(--shadow-sm);
        }
        .live-time .pulse-dot { width:8px; height:8px; border-radius:50%; background:var(--success); box-shadow:0 0 0 0 rgba(75,127,82,0.5); animation:pulse 2s infinite; }
        @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(75,127,82,0.5);} 70%{box-shadow:0 0 0 8px rgba(75,127,82,0);} 100%{box-shadow:0 0 0 0 rgba(75,127,82,0);} }

        /* ============ LAYOUT ============ */
        .page-wrap { max-width:880px; margin:0 auto; padding:1.75rem 1.25rem 3rem; }
        .page-head { margin-bottom:1.5rem; text-align:center; }
        .page-head h1 { font-family:'Plus Jakarta Sans',sans-serif; font-size:1.75rem; font-weight:800; color:var(--gray-900); margin:0 0 6px; letter-spacing:-0.02em; }
        .page-head p { color:var(--gray-500); font-size:0.9rem; margin:0; }

        /* ============ WORKER HERO CARD ============ */
        .worker-card {
            background: linear-gradient(135deg, #2c2820 0%, #5f4e30 100%);
            color:#fff; border-radius:var(--radius-xl); padding:1.5rem;
            margin-bottom:1.5rem; position:relative; overflow:hidden; box-shadow:var(--shadow-xl);
        }
        .worker-card::before { content:''; position:absolute; top:-50%; right:-10%; width:300px; height:300px; background:radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events:none; }
        .worker-card::after  { content:''; position:absolute; bottom:-40%; left:-10%; width:250px; height:250px; background:radial-gradient(circle, rgba(154,128,83,0.22) 0%, transparent 70%); pointer-events:none; }
        .worker-card-inner { position:relative; display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
        .worker-avatar {
            width:60px; height:60px; border-radius:16px;
            background: linear-gradient(135deg, #c9a86a, var(--primary));
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:1.3rem; color:#fff;
            font-family:'Plus Jakarta Sans',sans-serif;
            box-shadow:0 8px 16px rgba(0,0,0,0.25); flex-shrink:0;
        }
        .worker-info { flex:1; min-width:0; }
        .worker-info h3 { margin:0; font-family:'Plus Jakarta Sans',sans-serif; font-size:1.1rem; font-weight:700; }
        .worker-info .worker-meta { display:flex; gap:12px; margin-top:6px; font-size:0.78rem; color:rgba(255,255,255,0.72); flex-wrap:wrap; }
        .worker-info .worker-meta span { display:inline-flex; align-items:center; gap:4px; }
        .worker-info .worker-meta svg { width:12px; height:12px; }
        .sr-tag { background:rgba(255,255,255,0.12); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.18); padding:8px 14px; border-radius:12px; font-size:0.75rem; }
        .sr-tag .sr-label { color:rgba(255,255,255,0.6); font-size:0.65rem; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:2px; }
        .sr-tag .sr-id { font-weight:700; font-size:0.9rem; font-family:'Plus Jakarta Sans',sans-serif; }

        /* ============ STEPS ============ */
        .steps { display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:1.75rem; flex-wrap:wrap; }
        .step-item { display:flex; align-items:center; gap:10px; background:#fff; padding:8px 16px; border-radius:999px; box-shadow:var(--shadow-sm); border:1px solid var(--border); font-size:0.78rem; font-weight:600; color:var(--gray-500); }
        .step-item.active { background:var(--gray-900); color:#fff; border-color:var(--gray-900); }
        .step-item.done { background:var(--success-light); color:var(--success-dark); border-color:transparent; }
        .step-num { width:22px; height:22px; border-radius:50%; background:var(--gray-100); color:var(--gray-500); display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; }
        .step-item.active .step-num { background:#fff; color:var(--gray-900); }
        .step-item.done .step-num { background:var(--success); color:#fff; }
        .step-divider { width:24px; height:2px; background:var(--gray-200); border-radius:2px; }

        /* ============ SECTION CARDS ============ */
        .section-card { background:var(--card-bg); border-radius:var(--radius-lg); box-shadow:var(--shadow); margin-bottom:1.25rem; overflow:hidden; border:1px solid var(--border); }
        .section-head { padding:1.1rem 1.25rem; display:flex; align-items:center; gap:14px; border-bottom:1px solid var(--gray-100); background:linear-gradient(180deg, #fbfaf7, #fff); }
        .section-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#fff; box-shadow:var(--shadow-md); }
        .section-icon.start  { background:linear-gradient(135deg, var(--info), #4a483c); }
        .section-icon.detail { background:linear-gradient(135deg, var(--primary), var(--primary-darker)); }
        .section-icon.finish { background:linear-gradient(135deg, var(--success), var(--success-dark)); }
        .section-icon svg { width:20px; height:20px; }
        .section-head-text { flex:1; }
        .section-title { font-family:'Plus Jakarta Sans',sans-serif; font-size:1rem; font-weight:700; color:var(--gray-900); margin:0; }
        .section-sub { font-size:0.78rem; color:var(--gray-500); margin-top:2px; }
        .section-badge { padding:4px 10px; border-radius:999px; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
        .section-badge.required { background:var(--danger-light); color:var(--danger); }
        .section-badge.optional { background:var(--gray-100); color:var(--gray-600); }
        .section-badge.done { background:var(--success-light); color:var(--success-dark); }
        .section-body { padding:1.5rem 1.25rem; display:flex; flex-direction:column; gap:1.1rem; }

        /* ============ FORM ELEMENTS ============ */
        .field { display:flex; flex-direction:column; gap:6px; }
        .field-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .label { font-size:0.78rem; font-weight:600; color:var(--gray-700); display:flex; align-items:center; gap:4px; }
        .label .req { color:var(--danger); }
        .input, .textarea, .select {
            width:100%; padding:11px 14px; background:var(--gray-50);
            border:1.5px solid var(--gray-200); border-radius:var(--radius-sm);
            font-size:0.875rem; font-family:inherit; color:var(--gray-900);
            outline:none; transition:all 0.15s;
        }
        .input:focus, .textarea:focus, .select:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 4px var(--primary-glow); }
        .input::placeholder, .textarea::placeholder { color:var(--gray-400); }
        .input:disabled, .textarea:disabled { background:var(--gray-100); color:var(--gray-500); cursor:not-allowed; }
        .textarea { min-height:90px; resize:vertical; line-height:1.55; }
        .hint { font-size:0.72rem; color:var(--gray-500); line-height:1.4; }

        /* ============ PHOTO UPLOADER ============ */
        .photo-uploader { position:relative; border:2px dashed var(--gray-300); border-radius:var(--radius); background:linear-gradient(135deg, #fbfaf7 0%, #f3f1ea 100%); padding:1.75rem 1.25rem; text-align:center; cursor:pointer; transition:all 0.2s; overflow:hidden; }
        .photo-uploader:hover { border-color:var(--primary); background:var(--primary-light); }
        .photo-uploader.has-image { padding:0; border-style:solid; border-color:var(--gray-200); background:var(--gray-900); min-height:220px; }
        .photo-uploader input[type="file"] { display:none; }
        .uploader-icon { width:56px; height:56px; margin:0 auto 0.8rem; border-radius:16px; background:#fff; color:var(--primary); display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow); }
        .uploader-icon svg { width:26px; height:26px; }
        .uploader-title { font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:0.95rem; color:var(--gray-900); margin-bottom:4px; }
        .uploader-sub { font-size:0.75rem; color:var(--gray-500); }
        .uploader-btn { display:inline-flex; align-items:center; gap:6px; margin-top:12px; padding:8px 16px; background:var(--gray-900); color:#fff; border-radius:8px; font-size:0.8rem; font-weight:600; }
        .uploader-btn svg { width:14px; height:14px; }
        .photo-preview { position:relative; width:100%; height:100%; min-height:220px; }
        .photo-preview img { width:100%; height:100%; max-height:320px; object-fit:cover; display:block; }
        .photo-overlay { position:absolute; top:10px; right:10px; display:flex; gap:6px; }
        .photo-action { background:rgba(255,255,255,0.95); border:none; width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--gray-700); box-shadow:var(--shadow); transition:all 0.15s; }
        .photo-action:hover { background:#fff; color:var(--danger); }
        .photo-action svg { width:16px; height:16px; }
        .photo-meta-bar { position:absolute; bottom:0; left:0; right:0; background:linear-gradient(0deg, rgba(0,0,0,0.85) 0%, transparent 100%); padding:14px; color:#fff; display:flex; justify-content:space-between; align-items:end; font-size:0.72rem; flex-wrap:wrap; gap:6px; }
        .photo-meta-bar .meta-item { display:inline-flex; align-items:center; gap:4px; background:rgba(0,0,0,0.4); backdrop-filter:blur(8px); padding:4px 9px; border-radius:999px; font-weight:500; }
        .photo-meta-bar .meta-item svg { width:11px; height:11px; }

        /* ============ EQUIPMENT LIST ============ */
        .equipment-list { display:flex; flex-direction:column; gap:8px; }
        .equipment-row { display:grid; grid-template-columns:1fr 64px 90px 92px 36px; gap:8px; align-items:center; }
        .equipment-row .input { padding:10px 12px; font-size:0.825rem; }
        .equipment-row .input.num-input { text-align:center; }
        .equipment-row .input.rate-input { text-align:right; }
        .equipment-row .line-total { text-align:right; font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:0.9rem; color:var(--gray-900); background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:8px; padding:10px 12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .equipment-head { display:grid; grid-template-columns:1fr 64px 90px 92px 36px; gap:8px; padding:0 4px 2px; font-size:0.66rem; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:0.06em; }
        .equipment-head span:nth-child(2), .equipment-head span:nth-child(3), .equipment-head span:nth-child(4) { text-align:right; }
        .remove-btn { width:36px; height:38px; border:1.5px solid var(--gray-200); background:#fff; border-radius:8px; color:var(--gray-400); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.15s; }
        .remove-btn:hover { border-color:var(--danger); color:var(--danger); background:var(--danger-light); }
        .remove-btn svg { width:15px; height:15px; }
        .add-equipment-btn { display:inline-flex; align-items:center; gap:8px; align-self:flex-start; padding:9px 16px; background:#fff; border:1.5px dashed var(--primary); color:var(--primary-dark); border-radius:var(--radius-sm); font-size:0.825rem; font-weight:600; cursor:pointer; transition:all 0.15s; font-family:inherit; }
        .add-equipment-btn:hover { background:var(--primary-light); }
        .add-equipment-btn svg { width:14px; height:14px; }

        /* ============ BILL SUMMARY ============ */
        .bill-summary { background:linear-gradient(150deg, var(--primary-light), #fff); border:1px solid var(--primary-tint); border-radius:var(--radius); padding:1.1rem 1.25rem; }
        .bill-header { display:flex; align-items:center; gap:8px; font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; color:var(--gray-800); margin-bottom:12px; font-size:0.9rem; }
        .bill-header svg { width:16px; height:16px; color:var(--primary); }
        .bill-rows { display:flex; flex-direction:column; gap:6px; }
        .bill-row { display:flex; justify-content:space-between; align-items:center; font-size:0.82rem; color:var(--gray-700); }
        .bill-row-label { display:flex; flex-direction:column; }
        .bill-row-qty { font-size:0.7rem; color:var(--gray-400); }
        .bill-row-amount { font-weight:600; font-variant-numeric:tabular-nums; }
        .bill-divider { height:1px; background:var(--primary-tint); margin:12px 0; }
        .bill-divider.strong { height:2px; background:var(--gray-300); }
        .bill-line { display:flex; justify-content:space-between; align-items:center; font-size:0.85rem; color:var(--gray-600); margin-bottom:6px; }
        .bill-amount { font-weight:600; font-variant-numeric:tabular-nums; }
        .bill-input { width:120px; padding:7px 11px; text-align:right; background:#fff; border:1.5px solid var(--gray-200); border-radius:8px; font-family:inherit; font-size:0.85rem; font-weight:600; color:var(--gray-900); outline:none; }
        .bill-input:focus { border-color:var(--primary); box-shadow:0 0 0 3px var(--primary-glow); }
        .bill-total { display:flex; justify-content:space-between; align-items:center; }
        .bill-total-label { font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; color:var(--gray-800); }
        .bill-total-amount { font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:1.75rem; color:var(--primary-dark); font-variant-numeric:tabular-nums; }
        .bill-foot { display:flex; align-items:center; gap:8px; margin-top:12px; font-size:0.72rem; color:var(--gray-500); }
        .bill-foot svg { width:13px; height:13px; flex-shrink:0; }

        /* ============ SR CONTEXT CARD ============ */
        .sr-context { background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); padding:1rem 1.25rem; margin-bottom:1.25rem; }
        .sr-context-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:14px 20px; }
        .sr-ctx-item .ctx-label { font-size:0.66rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--gray-400); margin-bottom:3px; }
        .sr-ctx-item .ctx-value { font-size:0.85rem; font-weight:600; color:var(--gray-800); display:flex; align-items:center; gap:6px; }
        .sr-ctx-item .ctx-value svg { width:13px; height:13px; color:var(--primary); flex-shrink:0; }
        .sr-ctx-item.wide { grid-column:1 / -1; }
        .sr-ctx-item .ctx-value.desc { font-weight:500; color:var(--gray-600); line-height:1.5; }
        .prio-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:999px; font-size:0.72rem; font-weight:700; }
        .prio-pill.high, .prio-pill.urgent, .prio-pill.critical { background:var(--danger-light); color:var(--danger); }
        .prio-pill.medium, .prio-pill.normal { background:var(--warning-light); color:#7a5410; }
        .prio-pill.low { background:var(--success-light); color:var(--success-dark); }

        /* ============ STATUS BANNER ============ */
        .status-banner { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:var(--radius-sm); margin-bottom:1.25rem; }
        .status-banner.info { background:var(--info-light); color:#4a483c; }
        .status-banner.warning { background:var(--warning-light); color:#7a5410; }
        .status-banner.success { background:var(--success-light); color:var(--success-dark); }
        .status-banner svg { width:18px; height:18px; flex-shrink:0; }
        .status-banner .banner-text { font-size:0.825rem; font-weight:500; line-height:1.4; }
        .status-banner .banner-text strong { font-weight:700; }

        /* ============ TIMESTAMP CARD ============ */
        .timestamp-card { background:linear-gradient(135deg, #fbfaf7 0%, #fff 100%); border:1px solid var(--border); border-radius:var(--radius-sm); padding:14px 16px; display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .timestamp-item .ts-label { font-size:0.7rem; color:var(--gray-500); text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:3px; }
        .timestamp-item .ts-value { font-family:'Plus Jakarta Sans',sans-serif; font-size:1rem; font-weight:700; color:var(--gray-900); display:flex; align-items:center; gap:6px; }
        .timestamp-item .ts-value svg { width:14px; height:14px; color:var(--gray-500); }

        /* ============ LOCATION CHIP ============ */
        .location-chip { display:inline-flex; align-items:center; gap:8px; padding:8px 14px; background:var(--success-light); border:1px solid #a9cbac; border-radius:999px; color:var(--success-dark); font-size:0.78rem; font-weight:600; }
        .location-chip svg { width:13px; height:13px; }

        /* ============ FOOTER ACTIONS ============ */
        .footer-actions { display:flex; gap:12px; margin-top:1.5rem; flex-wrap:wrap; }
        .btn { flex:1; padding:13px 24px; border-radius:var(--radius-sm); border:none; font-size:0.9rem; font-weight:600; cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; justify-content:center; gap:8px; transition:all 0.15s; min-width:140px; text-decoration:none; }
        .btn-cancel { background:#fff; color:var(--gray-700); border:1.5px solid var(--gray-200); }
        .btn-cancel:hover { background:var(--gray-50); }
        .btn-save-draft { background:var(--gray-100); color:var(--gray-700); }
        .btn-save-draft:hover { background:var(--gray-200); }
        .btn-submit { background:linear-gradient(135deg, var(--primary) 0%, var(--primary-darker) 100%); color:#fff; box-shadow:0 8px 16px var(--primary-glow); }
        .btn-submit:hover { transform:translateY(-1px); box-shadow:0 12px 22px var(--primary-glow); }
        .btn-submit:active { transform:translateY(0); }
        .btn svg { width:16px; height:16px; }

        /* ============ VALIDATION ERRORS ============ */
        .error-box { background:var(--danger-light); border:1px solid #e0a99e; color:#7a2a1c; border-radius:var(--radius-sm); padding:12px 16px; margin-bottom:1.25rem; font-size:0.82rem; }
        .error-box ul { margin:6px 0 0; padding-left:18px; }

        /* ============ RESPONSIVE ============ */
        @media (max-width:640px) {
            .page-wrap { padding:1rem 0.85rem 2.5rem; }
            .page-head h1 { font-size:1.4rem; }
            .field-row { grid-template-columns:1fr; gap:1.1rem; }
            .section-head { padding:1rem; }
            .section-body { padding:1.25rem 1rem; }
            .equipment-row { grid-template-columns:1fr 60px 36px; grid-template-areas:"name name name" "qty rate remove" "total total total"; gap:6px; }
            .equipment-row .input:nth-child(1) { grid-area:name; }
            .equipment-row .input:nth-child(2) { grid-area:qty; }
            .equipment-row .input:nth-child(3) { grid-area:rate; }
            .equipment-row .line-total { grid-area:total; text-align:right; }
            .equipment-row .remove-btn { grid-area:remove; }
            .equipment-head { display:none; }
            .bill-input { width:100px; }
            .bill-total-amount { font-size:1.55rem; }
            .footer-actions { flex-direction:column; }
            .btn { width:100%; }
            .topbar { padding:0.75rem 1rem; }
            .topbar-brand .brand-text { font-size:0.85rem; }
            .live-time { font-size:0.72rem; padding:6px 11px; }
            .worker-card-inner { gap:12px; }
            .sr-tag { width:100%; }
            .steps { gap:6px; }
            .step-item { padding:6px 12px; font-size:0.72rem; }
            .step-divider { width:12px; }
        }
    </style>
</head>
<body>

    <!-- ============ TOP BAR ============ -->
    <div class="topbar">
        <a href="{{ url('/') }}" class="topbar-brand">
            <div class="brand-mark">SR</div>
            <div class="brand-text">
                Smart SR Portal
                <small>WORKER PUNCH</small>
            </div>
        </a>
        <div class="live-time">
            <span class="pulse-dot"></span>
            <span id="liveClock">--:--:--</span>
        </div>
    </div>

    <!-- ============ MAIN ============ -->
    <div class="page-wrap">

        <div class="page-head">
            <h1>Punch In &amp; Punch Out</h1>
            <p>Record your work start, equipment used, and completion proof.</p>
        </div>

        <!-- WORKER HERO (dynamic) -->
        <div class="worker-card">
            <div class="worker-card-inner">
                <div class="worker-avatar">{{ $worker->initials }}</div>
                <div class="worker-info">
                    <h3>{{ $worker->name }}</h3>
                    <div class="worker-meta">
                        <span><i data-feather="hash"></i>UID-{{ str_pad($worker->id, 4, '0', STR_PAD_LEFT) }}</span>
                        @if($worker->tradeLabel)
                            <span><i data-feather="briefcase"></i>{{ $worker->tradeLabel }}</span>
                        @endif
                        @if($worker->email)
                            <span><i data-feather="mail"></i>{{ $worker->email }}</span>
                        @endif
                    </div>
                </div>
                <div class="sr-tag">
                    <div class="sr-label">Service Request</div>
                    <div class="sr-id">{{ $sr->ref }}</div>
                </div>
            </div>
        </div>

        <!-- STEP INDICATOR (dynamic by status) -->
        @php
            // Stage derived from punch lifecycle so it never depends on SR status strings.
            // 1 = assigned/dispatched, 2 = punched in, 3 = work done, 4 = submitted
            if ($isSubmitted) {
                $stage = 4;
            } elseif ($isPunchedIn) {
                $stage = 2;
            } else {
                $stage = 1;
            }
        @endphp
        <div class="steps">
            <div class="step-item {{ $stage > 1 ? 'done' : ($stage === 1 ? 'active' : '') }}">
                <div class="step-num">@if($stage > 1)<i data-feather="check" style="width:12px;height:12px;"></i>@else 1 @endif</div>Assigned
            </div>
            <div class="step-divider"></div>
            <div class="step-item {{ $stage > 2 ? 'done' : ($stage === 2 ? 'active' : '') }}">
                <div class="step-num">@if($stage > 2)<i data-feather="check" style="width:12px;height:12px;"></i>@else 2 @endif</div>Punch In
            </div>
            <div class="step-divider"></div>
            <div class="step-item {{ $stage > 3 ? 'done' : ($stage === 3 ? 'active' : '') }}">
                <div class="step-num">@if($stage > 3)<i data-feather="check" style="width:12px;height:12px;"></i>@else 3 @endif</div>Work Done
            </div>
            <div class="step-divider"></div>
            <div class="step-item {{ $stage >= 4 ? 'done' : '' }}">
                <div class="step-num">@if($stage >= 4)<i data-feather="check" style="width:12px;height:12px;"></i>@else 4 @endif</div>Submitted
            </div>
        </div>

        <!-- FLASH / ERRORS -->
        @if(session('ok'))
            <div class="status-banner success">
                <i data-feather="check-circle"></i>
                <div class="banner-text">{{ session('ok') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="error-box">
                <strong>Please fix the following:</strong>
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- SR DISPATCH CONTEXT (from DB) -->
        <div class="sr-context">
            <div class="sr-context-grid">
                @if($sr->client)
                    <div class="sr-ctx-item">
                        <div class="ctx-label">Client</div>
                        <div class="ctx-value"><i data-feather="user"></i>{{ $sr->client->company_name ?? '—' }}</div>
                    </div>
                @endif
                @if($sr->project)
                    <div class="sr-ctx-item">
                        <div class="ctx-label">Project</div>
                        <div class="ctx-value"><i data-feather="folder"></i>{{ $sr->project->project_name ?? '—' }}</div>
                    </div>
                @endif
                @if($sr->category)
                    <div class="sr-ctx-item">
                        <div class="ctx-label">Service Type</div>
                        <div class="ctx-value"><i data-feather="tag"></i>{{ $sr->category->category_name ?? '—' }}</div>
                    </div>
                @endif
                @if($sr->priority_level)
                    <div class="sr-ctx-item">
                        <div class="ctx-label">Priority</div>
                        <div class="ctx-value">
                            <span class="prio-pill {{ strtolower($sr->priority_level) }}">
                                <i data-feather="alert-triangle" style="width:11px;height:11px;"></i>{{ ucfirst($sr->priority_level) }}
                            </span>
                        </div>
                    </div>
                @endif
                @if($sr->dispatched_at)
                    <div class="sr-ctx-item">
                        <div class="ctx-label">Dispatched</div>
                        <div class="ctx-value"><i data-feather="send"></i>{{ \Illuminate\Support\Carbon::parse($sr->dispatched_at)->format('d M Y, g:i A') }}</div>
                    </div>
                @endif
                @if($sr->issue_description)
                    <div class="sr-ctx-item wide">
                        <div class="ctx-label">Reported Issue</div>
                        <div class="ctx-value desc">{{ $sr->issue_description }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="status-banner info">
            <i data-feather="info"></i>
            <div class="banner-text">
                <strong>Heads up:</strong> Both start and completion photos are mandatory. Make sure equipment is visible in the completion photo.
            </div>
        </div>

        <!-- ============ SECTION 1: PUNCH IN ============ -->
        <form id="punchInForm" method="POST"
              action="{{ route('worker.punch.in', $sr) }}" enctype="multipart/form-data">
            @csrf
            <div class="section-card">
                <div class="section-head">
                    <div class="section-icon start"><i data-feather="play-circle"></i></div>
                    <div class="section-head-text">
                        <h3 class="section-title">Punch In — Start of Work</h3>
                        <div class="section-sub">Capture site condition before work begins</div>
                    </div>
                    <div class="section-badge {{ $isPunchedIn ? 'done' : 'required' }}">
                        {{ $isPunchedIn ? 'Done' : 'Required' }}
                    </div>
                </div>

                <div class="section-body">
                    <div class="timestamp-card">
                        <div class="timestamp-item">
                            <div class="ts-label">Punch In Time</div>
                            <div class="ts-value">
                                <i data-feather="clock"></i>
                                <span id="punchInTime">
                                    {{ $isPunchedIn ? $punch->punch_in_at->format('g:i A') : '--:-- --' }}
                                </span>
                            </div>
                        </div>
                        <div class="timestamp-item">
                            <div class="ts-label">Date</div>
                            <div class="ts-value">
                                <i data-feather="calendar"></i>
                                <span id="punchInDate">
                                    {{ $isPunchedIn ? $punch->punch_in_at->format('d M Y') : now()->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Site Location <span class="req">*</span></label>
                        <input type="text" name="site_location" class="input"
                               value="{{ old('site_location', $punch->site_location ?? optional($sr->project)->site_address) }}"
                               placeholder="Enter the on-site work address"
                               {{ $isPunchedIn ? 'disabled' : 'required' }}>
                        @if($isPunchedIn && $punch->start_gps_lat)
                            <div class="hint">
                                <span class="location-chip">
                                    <i data-feather="map-pin"></i>
                                    GPS: {{ number_format($punch->start_gps_lat, 4) }}°N, {{ number_format($punch->start_gps_lng, 4) }}°E
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="field">
                        <label class="label">Brief Description of Work <span class="req">*</span></label>
                        <textarea name="work_description" class="textarea"
                                  placeholder="e.g. Replacing leaking pipe in 3rd floor washroom..."
                                  {{ $isPunchedIn ? 'disabled' : 'required' }}>{{ old('work_description', $punch->work_description ?? $sr->issue_description) }}</textarea>
                        <span class="hint">Be specific — this helps QC review later.</span>
                    </div>

                    <div class="field">
                        <label class="label">Start Photo (Before Work) <span class="req">*</span></label>
                        @if($isPunchedIn && $punch->start_photo_path)
                            <div class="photo-uploader has-image">
                                <div class="photo-preview" style="display:block;">
                                    <img src="{{ Storage::url($punch->start_photo_path) }}" alt="Start of work">
                                    <div class="photo-meta-bar">
                                        <span class="meta-item"><i data-feather="clock"></i>{{ $punch->punch_in_at->format('H:i') }}</span>
                                        @if($punch->start_gps_lat)
                                            <span class="meta-item"><i data-feather="map-pin"></i>{{ number_format($punch->start_gps_lat,2) }}°N, {{ number_format($punch->start_gps_lng,2) }}°E</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <label class="photo-uploader" id="startUploader">
                                <input type="file" name="start_photo" accept="image/*" capture="environment"
                                       onchange="previewPhoto(event,'start')" required>
                                <div id="startUploaderEmpty">
                                    <div class="uploader-icon"><i data-feather="camera"></i></div>
                                    <div class="uploader-title">Tap to capture start photo</div>
                                    <div class="uploader-sub">Show the site condition before you begin</div>
                                    <div class="uploader-btn"><i data-feather="camera"></i>Open Camera</div>
                                </div>
                                <div class="photo-preview" id="startPreview" style="display:none;">
                                    <img id="startImg" src="" alt="Start of work">
                                    <div class="photo-overlay">
                                        <button type="button" class="photo-action" onclick="event.preventDefault();removePhoto('start')" title="Remove"><i data-feather="trash-2"></i></button>
                                    </div>
                                    <div class="photo-meta-bar">
                                        <span class="meta-item"><i data-feather="clock"></i><span id="startStampTime">--:--</span></span>
                                        <span class="meta-item"><i data-feather="map-pin"></i><span id="startGeo">locating…</span></span>
                                    </div>
                                </div>
                            </label>
                            <input type="hidden" name="start_gps_lat" id="start_gps_lat">
                            <input type="hidden" name="start_gps_lng" id="start_gps_lng">
                            <span class="hint">Photo must be taken at site — geo-tag &amp; timestamp will be recorded.</span>
                        @endif
                    </div>

                    @unless($isPunchedIn)
                        <button type="submit" class="btn btn-submit" style="align-self:flex-start;flex:0;">
                            <i data-feather="play-circle"></i>Punch In Now
                        </button>
                    @endunless
                </div>
            </div>
        </form>

        <!-- ============ PUNCH OUT FORM (sections 2 + 3) ============ -->
        <form id="punchOutForm" method="POST"
              action="{{ route('worker.punch.submit', $sr) }}" enctype="multipart/form-data">
            @csrf

            <!-- ===== SECTION 2: EQUIPMENT & MATERIALS ===== -->
            <div class="section-card">
                <div class="section-head">
                    <div class="section-icon detail"><i data-feather="package"></i></div>
                    <div class="section-head-text">
                        <h3 class="section-title">Equipment, Materials &amp; Cost</h3>
                        <div class="section-sub">List items used — bill total updates live</div>
                    </div>
                    <div class="section-badge required">Required</div>
                </div>

                <div class="section-body">
                    <div class="equipment-head">
                        <span>Item / Material</span><span>Qty</span><span>Rate (₹)</span><span>Amount</span><span></span>
                    </div>

                    <div class="equipment-list" id="equipmentList">
                        @forelse($items as $item)
                            <div class="equipment-row">
                                <input type="text" name="item_name[]" class="input" placeholder="Item / material name" value="{{ $item->name }}" oninput="recalc()">
                                <input type="number" name="item_qty[]" class="input num-input" placeholder="Qty" value="{{ rtrim(rtrim((string)$item->qty,'0'),'.') }}" min="0" oninput="recalc()">
                                <input type="number" name="item_rate[]" class="input rate-input" placeholder="Rate" value="{{ rtrim(rtrim((string)$item->rate,'0'),'.') }}" min="0" step="0.01" oninput="recalc()">
                                <div class="line-total">{{ '₹'.number_format($item->line_total,2) }}</div>
                                <button type="button" class="remove-btn" onclick="removeEquipmentRow(this)"><i data-feather="x"></i></button>
                            </div>
                        @empty
                            <div class="equipment-row">
                                <input type="text" name="item_name[]" class="input" placeholder="Item / material name" oninput="recalc()">
                                <input type="number" name="item_qty[]" class="input num-input" placeholder="Qty" min="0" oninput="recalc()">
                                <input type="number" name="item_rate[]" class="input rate-input" placeholder="Rate" min="0" step="0.01" oninput="recalc()">
                                <div class="line-total">₹0.00</div>
                                <button type="button" class="remove-btn" onclick="removeEquipmentRow(this)"><i data-feather="x"></i></button>
                            </div>
                        @endforelse
                    </div>

                    <button type="button" class="add-equipment-btn" onclick="addEquipmentRow()">
                        <i data-feather="plus"></i>Add another item
                    </button>

                    <div class="bill-summary">
                        <div class="bill-header"><i data-feather="file-text"></i><span>Estimated Bill Summary</span></div>
                        <div class="bill-rows" id="billRows"></div>
                        <div class="bill-divider"></div>
                        <div class="bill-line">
                            <span class="bill-label">Materials Subtotal</span>
                            <span class="bill-amount" id="materialsSubtotal">₹0.00</span>
                        </div>
                        <div class="bill-line">
                            <span class="bill-label">Labour / Service Charge</span>
                            <input type="number" class="bill-input" id="labourInput" name="labour_charge"
                                   value="{{ old('labour_charge', $punch->labour_charge ?? 0) }}" min="0" step="0.01" oninput="recalc()">
                        </div>
                        <div class="bill-divider strong"></div>
                        <div class="bill-total">
                            <span class="bill-total-label">Total Estimated Cost</span>
                            <span class="bill-total-amount" id="grandTotal">₹0.00</span>
                        </div>
                        <div class="bill-foot"><i data-feather="info"></i><span>This estimate is added to the expense log and shared with the client for approval.</span></div>
                    </div>

                    <div class="field">
                        <label class="label">Receipt / Bill Number</label>
                        <input type="text" name="receipt_number" class="input" placeholder="Optional bill / invoice reference" value="{{ old('receipt_number', $punch->receipt_number) }}">
                    </div>

                    <div class="field">
                        <label class="label">Notes</label>
                        <textarea name="notes" class="textarea" placeholder="Extra parts needed, follow-up required, observations...">{{ old('notes', $punch->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ===== SECTION 3: PUNCH OUT ===== -->
            <div class="section-card">
                <div class="section-head">
                    <div class="section-icon finish"><i data-feather="check-circle"></i></div>
                    <div class="section-head-text">
                        <h3 class="section-title">Punch Out — Work Completed</h3>
                        <div class="section-sub">Capture completed work + equipment shown in frame</div>
                    </div>
                    <div class="section-badge required">Required</div>
                </div>

                <div class="section-body">
                    <div class="timestamp-card">
                        <div class="timestamp-item">
                            <div class="ts-label">Punch Out Time</div>
                            <div class="ts-value"><i data-feather="clock"></i><span id="punchOutTime">{{ $punch->punch_out_at?->format('g:i A') ?? 'Set on submit' }}</span></div>
                        </div>
                        <div class="timestamp-item">
                            <div class="ts-label">Duration</div>
                            <div class="ts-value"><i data-feather="watch"></i><span id="duration">{{ $punch->duration_label ?? 'Auto' }}</span></div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Completion Photo (After Work) <span class="req">*</span></label>
                        <label class="photo-uploader" id="finishUploader">
                            <input type="file" name="finish_photo" accept="image/*" capture="environment" onchange="previewPhoto(event,'finish')" {{ $isPunchedIn ? 'required' : 'disabled' }}>
                            <div id="finishUploaderEmpty">
                                <div class="uploader-icon" style="color:var(--success);"><i data-feather="camera"></i></div>
                                <div class="uploader-title">Tap to capture completion photo</div>
                                <div class="uploader-sub">Show finished work — equipment must be visible</div>
                                <div class="uploader-btn" style="background:var(--success);"><i data-feather="camera"></i>Open Camera</div>
                            </div>
                            <div class="photo-preview" id="finishPreview" style="display:none;">
                                <img id="finishImg" src="" alt="Completed work">
                                <div class="photo-overlay">
                                    <button type="button" class="photo-action" onclick="event.preventDefault();removePhoto('finish')" title="Remove"><i data-feather="trash-2"></i></button>
                                </div>
                                <div class="photo-meta-bar">
                                    <span class="meta-item"><i data-feather="clock"></i><span id="finishStampTime">--:--</span></span>
                                    <span class="meta-item"><i data-feather="map-pin"></i><span id="finishGeo">locating…</span></span>
                                </div>
                            </div>
                        </label>
                        <input type="hidden" name="finish_gps_lat" id="finish_gps_lat">
                        <input type="hidden" name="finish_gps_lng" id="finish_gps_lng">
                        <div class="status-banner warning" style="margin-top:10px;margin-bottom:0;">
                            <i data-feather="alert-circle"></i>
                            <div class="banner-text"><strong>Tip:</strong> Place all equipment used in the frame so QC can verify materials.</div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Work Completion Summary <span class="req">*</span></label>
                        <textarea name="completion_summary" class="textarea" placeholder="e.g. Replaced 6 ft of leaking pipe with new PVC. Tested water flow — no leaks." {{ $isPunchedIn ? 'required' : 'disabled' }}>{{ old('completion_summary', $punch->completion_summary) }}</textarea>
                        <span class="hint">Will be shared with the client in the completion summary.</span>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label class="label">Customer Name (Sign-off) <span class="req">*</span></label>
                            <input type="text" name="customer_name" class="input" placeholder="Name of person who verified work" value="{{ old('customer_name', $punch->customer_name) }}" {{ $isPunchedIn ? 'required' : 'disabled' }}>
                        </div>
                        <div class="field">
                            <label class="label">Customer Phone</label>
                            <input type="tel" name="customer_phone" class="input" placeholder="+91 XXXXX XXXXX" value="{{ old('customer_phone', $punch->customer_phone) }}" {{ $isPunchedIn ? '' : 'disabled' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="footer-actions">
                <a href="{{ url()->previous() }}" class="btn btn-cancel"><i data-feather="arrow-left"></i>Cancel</a>
                <button type="submit" class="btn btn-save-draft" formaction="{{ route('worker.punch.draft', $sr) }}" {{ $isPunchedIn ? '' : 'disabled' }}>
                    <i data-feather="save"></i>Save Draft
                </button>
                <button type="submit" class="btn btn-submit" {{ $isPunchedIn ? '' : 'disabled' }}>
                    <i data-feather="send"></i>Submit Punch Out
                </button>
            </div>

            @unless($isPunchedIn)
                <div class="status-banner warning" style="margin-top:1rem;">
                    <i data-feather="lock"></i>
                    <div class="banner-text">Punch in first to unlock equipment logging and punch out.</div>
                </div>
            @endunless
        </form>
    </div>

    <script>
        feather.replace();
        const pad = n => n.toString().padStart(2,'0');

        // ===== LIVE CLOCK =====
        function tick(){
            const d = new Date();
            document.getElementById('liveClock').textContent = `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        }
        tick(); setInterval(tick, 1000);

        const ampm = h => h >= 12 ? 'PM' : 'AM';
        const to12 = h => h % 12 === 0 ? 12 : h % 12;
        const formatTime = d => `${to12(d.getHours())}:${pad(d.getMinutes())} ${ampm(d.getHours())}`;

        // Punch-in reference time (from DB if already punched in)
        const punchInISO = @json($isPunchedIn ? $punch->punch_in_at->toIso8601String() : null);
        const punchInDate = punchInISO ? new Date(punchInISO) : new Date();

        // ===== GEOLOCATION =====
        function captureGeo(type){
            if(!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude, lng = pos.coords.longitude;
                const latEl = document.getElementById(type+'_gps_lat');
                const lngEl = document.getElementById(type+'_gps_lng');
                if(latEl) latEl.value = lat.toFixed(7);
                if(lngEl) lngEl.value = lng.toFixed(7);
                const geoEl = document.getElementById(type+'Geo');
                if(geoEl) geoEl.textContent = `${lat.toFixed(2)}°N, ${lng.toFixed(2)}°E`;
            }, () => {
                const geoEl = document.getElementById(type+'Geo');
                if(geoEl) geoEl.textContent = 'GPS unavailable';
            });
        }

        // ===== PHOTO PREVIEW =====
        function previewPhoto(e, type){
            const file = e.target.files[0];
            if(!file) return;
            const url = URL.createObjectURL(file);
            document.getElementById(type+'Uploader').classList.add('has-image');
            document.getElementById(type+'UploaderEmpty').style.display = 'none';
            document.getElementById(type+'Preview').style.display = 'block';
            document.getElementById(type+'Img').src = url;

            const now = new Date();
            document.getElementById(type+'StampTime').textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
            captureGeo(type);

            if(type === 'finish'){
                document.getElementById('punchOutTime').textContent = formatTime(now);
                const diff = now - punchInDate;
                const hrs = Math.floor(diff/3600000), mins = Math.floor((diff%3600000)/60000);
                document.getElementById('duration').textContent = `${hrs}h ${pad(mins)}m`;
            }
            feather.replace();
        }

        function removePhoto(type){
            const up = document.getElementById(type+'Uploader');
            up.querySelector('input[type=file]').value = '';
            document.getElementById(type+'Preview').style.display = 'none';
            document.getElementById(type+'UploaderEmpty').style.display = 'block';
            up.classList.remove('has-image');
            if(type === 'finish'){
                document.getElementById('punchOutTime').textContent = 'Set on submit';
                document.getElementById('duration').textContent = 'Auto';
            }
        }

        // ===== EQUIPMENT ROWS =====
        function addEquipmentRow(){
            const list = document.getElementById('equipmentList');
            const row = document.createElement('div');
            row.className = 'equipment-row';
            row.innerHTML = `
                <input type="text" name="item_name[]" class="input" placeholder="Item / material name" oninput="recalc()">
                <input type="number" name="item_qty[]" class="input num-input" placeholder="Qty" min="0" oninput="recalc()">
                <input type="number" name="item_rate[]" class="input rate-input" placeholder="Rate" min="0" step="0.01" oninput="recalc()">
                <div class="line-total">₹0.00</div>
                <button type="button" class="remove-btn" onclick="removeEquipmentRow(this)"><i data-feather="x"></i></button>`;
            list.appendChild(row);
            feather.replace();
            row.querySelector('input').focus();
            recalc();
        }
        function removeEquipmentRow(btn){
            const list = document.getElementById('equipmentList');
            if(list.children.length <= 1) return;
            btn.closest('.equipment-row').remove();
            recalc();
        }

        // ===== LIVE BILL =====
        function escapeHtml(s){ return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
        function recalc(){
            const rows = document.querySelectorAll('#equipmentList .equipment-row');
            const billRowsEl = document.getElementById('billRows');
            let subtotal = 0; billRowsEl.innerHTML = '';
            rows.forEach(row => {
                const inp = row.querySelectorAll('input');
                const qty = parseFloat(inp[1].value) || 0;
                const rate = parseFloat(inp[2].value) || 0;
                const total = qty * rate;
                row.querySelector('.line-total').textContent = '₹'+total.toFixed(2);
                subtotal += total;
                const name = inp[0].value.trim();
                if(name && total > 0){
                    const br = document.createElement('div');
                    br.className = 'bill-row';
                    br.innerHTML = `<span class="bill-row-label">${escapeHtml(name)}<span class="bill-row-qty">× ${qty} @ ₹${rate.toFixed(2)}</span></span><span class="bill-row-amount">₹${total.toFixed(2)}</span>`;
                    billRowsEl.appendChild(br);
                }
            });
            document.getElementById('materialsSubtotal').textContent = '₹'+subtotal.toFixed(2);
            const labour = parseFloat(document.getElementById('labourInput').value) || 0;
            document.getElementById('grandTotal').textContent = '₹'+(subtotal+labour).toFixed(2);
        }
        recalc();

        // Pre-warm GPS for start photo if not yet punched in
        @unless($isPunchedIn)
            captureGeo('start');
        @endunless
    </script>
</body>
</html>