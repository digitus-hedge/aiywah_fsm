@extends('layouts.layout')

@section('title', 'Admin Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h4>Admin Dashboard</h4>
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard') }}">Home</a> &nbsp;/&nbsp; <span>Dashboard</span>
        </div>
    </div>
    <div style="display:flex; gap:8px;">
        <button class="btn-soft">
            <i data-feather="download" style="width:13px;height:13px;vertical-align:-2px;margin-right:4px;"></i>Export
        </button>
        <button class="btn-primary-sm">
            <i data-feather="plus" style="width:13px;height:13px;vertical-align:-2px;margin-right:4px;"></i>New SR
        </button>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="stat-icon primary"><i data-feather="file-text"></i></div>
            <div>
                <div class="stat-value">1,284</div>
                <div class="stat-label">Total SRs (This Month)</div>
                <div class="stat-trend up"><i data-feather="trending-up"></i>+12.4% vs last month</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="stat-icon info"><i data-feather="activity"></i></div>
            <div>
                <div class="stat-value">87</div>
                <div class="stat-label">In Progress</div>
                <div class="stat-trend up"><i data-feather="trending-up"></i>+5 since yesterday</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="stat-icon success"><i data-feather="check-circle"></i></div>
            <div>
                <div class="stat-value">912</div>
                <div class="stat-label">Completed</div>
                <div class="stat-trend up"><i data-feather="trending-up"></i>+8.2% vs last month</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="stat-icon warning"><i data-feather="alert-triangle"></i></div>
            <div>
                <div class="stat-value">23</div>
                <div class="stat-label">SLA at Risk</div>
                <div class="stat-trend down"><i data-feather="trending-down"></i>+3 vs yesterday</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mt-1">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">SR Volume &amp; Resolution Trend</h5>
                <div class="filter-tabs">
                    <button>7D</button>
                    <button class="active">30D</button>
                    <button>90D</button>
                    <button>1Y</button>
                </div>
            </div>
            <div class="card-body">
                <div id="trendChart"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">SR Status Distribution</h5>
            </div>
            <div class="card-body">
                <div id="statusChart"></div>
                <div style="margin-top:1rem; font-size: 0.75rem;">
                    <div style="display:flex; justify-content:space-between; padding:4px 0;"><span><span class="legend-dot" style="background:#0acf97;"></span>Completed</span><strong>912</strong></div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0;"><span><span class="legend-dot" style="background:#39afd1;"></span>In Progress</span><strong>87</strong></div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0;"><span><span class="legend-dot" style="background:#ffbc00;"></span>Pending Review</span><strong>34</strong></div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0;"><span><span class="legend-dot" style="background:#727cf5;"></span>Pending/Approved</span><strong>196</strong></div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0;"><span><span class="legend-dot" style="background:#fa5c7c;"></span>Rework/Cancelled</span><strong>55</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Table + Activity --}}
<div class="row g-3 mt-1">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Service Requests</h5>
                <a href="#" style="font-size:0.75rem; color: var(--primary); text-decoration:none;">View all →</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>SR ID</th>
                            <th>Client</th>
                            <th>Service Type</th>
                            <th>Assigned Lead</th>
                            <th>Status</th>
                            <th>ETA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a href="#" class="sr-id">SR-2026-1284</a></td>
                            <td><div class="client-cell"><div class="client-avatar">AC</div><div>Acme Corp<div class="text-muted-sm">Mumbai Site A</div></div></div></td>
                            <td>AC Repair</td><td>Rajesh K.</td>
                            <td><span class="badge-status badge-progress">IN PROGRESS</span></td>
                            <td>Today, 4:00 PM</td>
                        </tr>
                        <tr>
                            <td><a href="#" class="sr-id">SR-2026-1283</a></td>
                            <td><div class="client-cell"><div class="client-avatar">TI</div><div>Tata Industries<div class="text-muted-sm">Pune HQ</div></div></div></td>
                            <td>Network Setup</td><td>Priya N.</td>
                            <td><span class="badge-status badge-review">PENDING REVIEW</span></td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><a href="#" class="sr-id">SR-2026-1282</a></td>
                            <td><div class="client-cell"><div class="client-avatar">IN</div><div>Infosys Ltd<div class="text-muted-sm">Bangalore Tower 3</div></div></div></td>
                            <td>UPS Maintenance</td><td>Mohan S.</td>
                            <td><span class="badge-status badge-completed">COMPLETED</span></td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><a href="#" class="sr-id">SR-2026-1281</a></td>
                            <td><div class="client-cell"><div class="client-avatar">RG</div><div>Reliance Group<div class="text-muted-sm">Mumbai HQ</div></div></div></td>
                            <td>CCTV Install</td><td>—</td>
                            <td><span class="badge-status badge-quoted">QUOTED</span></td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><a href="#" class="sr-id">SR-2026-1280</a></td>
                            <td><div class="client-cell"><div class="client-avatar">WP</div><div>Wipro Tech<div class="text-muted-sm">Kochi Site B</div></div></div></td>
                            <td>AC Repair</td><td>Anil V.</td>
                            <td><span class="badge-status badge-rework">REWORK</span></td>
                            <td>Tomorrow, 11 AM</td>
                        </tr>
                        <tr>
                            <td><a href="#" class="sr-id">SR-2026-1279</a></td>
                            <td><div class="client-cell"><div class="client-avatar">HC</div><div>HCL Technologies<div class="text-muted-sm">Chennai Tower 1</div></div></div></td>
                            <td>Server Migration</td><td>Rajesh K.</td>
                            <td><span class="badge-status badge-assigned">ASSIGNED</span></td>
                            <td>23 Jun, 10 AM</td>
                        </tr>
                        <tr>
                            <td><a href="#" class="sr-id">SR-2026-1278</a></td>
                            <td><div class="client-cell"><div class="client-avatar">FL</div><div>Flipkart<div class="text-muted-sm">Bangalore DC</div></div></div></td>
                            <td>Printer Service</td><td>—</td>
                            <td><span class="badge-status badge-pending">PENDING</span></td>
                            <td>—</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Recent Activity</h5></div>
            <div class="card-body" style="padding-top:0.5rem;">
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-dot success"><i data-feather="check"></i></div>
                        <div class="activity-content"><strong>Mohan S.</strong> completed <strong>SR-2026-1282</strong><span class="time">2 minutes ago</span></div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-dot"><i data-feather="map-pin"></i></div>
                        <div class="activity-content"><strong>Rajesh K.</strong> punched in at Acme Corp, Mumbai<span class="time">12 minutes ago</span></div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-dot warning"><i data-feather="alert-circle"></i></div>
                        <div class="activity-content"><strong>SR-2026-1280</strong> sent back for rework by HoP<span class="time">38 minutes ago</span></div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-dot"><i data-feather="file-plus"></i></div>
                        <div class="activity-content">New SR created for <strong>Tata Industries</strong> by Front Desk<span class="time">1 hour ago</span></div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-dot success"><i data-feather="check-circle"></i></div>
                        <div class="activity-content">Quote for <strong>SR-2026-1281</strong> approved by client<span class="time">2 hours ago</span></div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-dot danger"><i data-feather="x-circle"></i></div>
                        <div class="activity-content"><strong>SR-2026-1275</strong> cancelled — out of coverage area<span class="time">3 hours ago</span></div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-dot"><i data-feather="user-plus"></i></div>
                        <div class="activity-content">New Maintenance Lead <strong>Suresh M.</strong> onboarded<span class="time">5 hours ago</span></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Lead Performance + Warranty --}}
<div class="row g-3 mt-1">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Top Performing Maintenance Leads</h5>
                <a href="#" style="font-size:0.75rem; color: var(--primary); text-decoration:none;">Full report →</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Lead</th><th>SRs Closed</th><th>Avg. Resolution</th><th>SLA Adherence</th><th>Rework Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><div class="client-cell"><div class="client-avatar" style="background:linear-gradient(135deg,#0acf97,#07a578);">RK</div><div>Rajesh Kumar<div class="text-muted-sm">AC &amp; HVAC</div></div></div></td>
                            <td>148</td><td>3h 12m</td>
                            <td><div style="display:flex;align-items:center;gap:8px;"><div class="progress-bar-thin"><div class="progress-bar-fill" style="width:96%;background:#0acf97;"></div></div><span style="font-size:0.75rem;">96%</span></div></td>
                            <td><span style="color:var(--success);">2.1%</span></td>
                        </tr>
                        <tr>
                            <td><div class="client-cell"><div class="client-avatar" style="background:linear-gradient(135deg,#39afd1,#1f7a96);">PN</div><div>Priya N.<div class="text-muted-sm">Networking</div></div></div></td>
                            <td>132</td><td>4h 05m</td>
                            <td><div style="display:flex;align-items:center;gap:8px;"><div class="progress-bar-thin"><div class="progress-bar-fill" style="width:92%;background:#0acf97;"></div></div><span style="font-size:0.75rem;">92%</span></div></td>
                            <td><span style="color:var(--success);">3.4%</span></td>
                        </tr>
                        <tr>
                            <td><div class="client-cell"><div class="client-avatar" style="background:linear-gradient(135deg,#727cf5,#4a55c7);">MS</div><div>Mohan S.<div class="text-muted-sm">UPS &amp; Power</div></div></div></td>
                            <td>118</td><td>3h 48m</td>
                            <td><div style="display:flex;align-items:center;gap:8px;"><div class="progress-bar-thin"><div class="progress-bar-fill" style="width:89%;background:#39afd1;"></div></div><span style="font-size:0.75rem;">89%</span></div></td>
                            <td><span style="color:var(--warning);">5.1%</span></td>
                        </tr>
                        <tr>
                            <td><div class="client-cell"><div class="client-avatar" style="background:linear-gradient(135deg,#ffbc00,#b88400);">AV</div><div>Anil V.<div class="text-muted-sm">AC &amp; HVAC</div></div></div></td>
                            <td>104</td><td>5h 22m</td>
                            <td><div style="display:flex;align-items:center;gap:8px;"><div class="progress-bar-thin"><div class="progress-bar-fill" style="width:84%;background:#39afd1;"></div></div><span style="font-size:0.75rem;">84%</span></div></td>
                            <td><span style="color:var(--warning);">7.8%</span></td>
                        </tr>
                        <tr>
                            <td><div class="client-cell"><div class="client-avatar" style="background:linear-gradient(135deg,#fa5c7c,#c8455f);">SM</div><div>Suresh M.<div class="text-muted-sm">CCTV &amp; Security</div></div></div></td>
                            <td>87</td><td>4h 41m</td>
                            <td><div style="display:flex;align-items:center;gap:8px;"><div class="progress-bar-thin"><div class="progress-bar-fill" style="width:78%;background:#ffbc00;"></div></div><span style="font-size:0.75rem;">78%</span></div></td>
                            <td><span style="color:var(--danger);">9.4%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Warranty Coverage Split</h5></div>
            <div class="card-body"><div id="warrantyChart"></div></div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="card-title">Quick Actions</h5></div>
            <div class="card-body" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <button class="btn-soft" style="padding:14px; text-align:left;">
                    <i data-feather="user-plus" style="width:16px;height:16px;color:var(--primary);"></i>
                    <div style="font-size:0.8rem;margin-top:6px;font-weight:500;">Add User</div>
                    <div style="font-size:0.7rem;color:var(--text-muted);">Onboard new staff</div>
                </button>
                <button class="btn-soft" style="padding:14px; text-align:left;">
                    <i data-feather="briefcase" style="width:16px;height:16px;color:var(--success);"></i>
                    <div style="font-size:0.8rem;margin-top:6px;font-weight:500;">New Client</div>
                    <div style="font-size:0.7rem;color:var(--text-muted);">Register a client</div>
                </button>
                <button class="btn-soft" style="padding:14px; text-align:left;">
                    <i data-feather="shield" style="width:16px;height:16px;color:var(--warning);"></i>
                    <div style="font-size:0.8rem;margin-top:6px;font-weight:500;">Permissions</div>
                    <div style="font-size:0.7rem;color:var(--text-muted);">Configure role access</div>
                </button>
                <button class="btn-soft" style="padding:14px; text-align:left;">
                    <i data-feather="bar-chart-2" style="width:16px;height:16px;color:var(--info);"></i>
                    <div style="font-size:0.8rem;margin-top:6px;font-weight:500;">Reports</div>
                    <div style="font-size:0.7rem;color:var(--text-muted);">View analytics</div>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Filter tabs
    document.querySelectorAll('.filter-tabs button').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.filter-tabs').querySelectorAll('button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Trend Chart
    new ApexCharts(document.querySelector("#trendChart"), {
        chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Roboto, sans-serif' },
        series: [
            { name: 'New SRs',   data: [42,38,55,48,62,58,71,65,52,48,68,75,82,71,68,72,85,78,69,74,88,92,85,79,86,94,88,82,91,96] },
            { name: 'Completed', data: [38,32,48,42,55,52,64,58,48,42,60,68,75,64,62,68,78,71,62,68,82,86,79,73,80,87,82,76,84,89] },
            { name: 'Rework',    data: [3,4,2,5,3,4,6,4,3,5,4,6,5,7,4,5,6,5,4,7,5,8,6,5,7,9,6,5,8,7] },
        ],
        colors: ['#727cf5','#0acf97','#fa5c7c'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity:1, opacityFrom:0.4, opacityTo:0.05, stops:[0,100] } },
        dataLabels: { enabled: false },
        xaxis: { categories: Array.from({length:30},(_,i)=>`D${i+1}`), labels:{style:{fontSize:'11px',colors:'#98a6ad'}}, axisBorder:{show:false}, axisTicks:{show:false} },
        yaxis: { labels: { style: { fontSize:'11px', colors:'#98a6ad' } } },
        grid: { borderColor:'#eef2f7', strokeDashArray:3 },
        legend: { position:'top', horizontalAlign:'right', fontSize:'12px', markers:{width:9,height:9,radius:9} },
        tooltip: { theme:'light', x:{show:false} },
    }).render();

    // Status Donut
    new ApexCharts(document.querySelector("#statusChart"), {
        chart: { type:'donut', height:230, fontFamily:'Roboto, sans-serif' },
        series: [912, 87, 34, 196, 55],
        labels: ['Completed','In Progress','Pending Review','Pending/Approved','Rework/Cancelled'],
        colors: ['#0acf97','#39afd1','#ffbc00','#727cf5','#fa5c7c'],
        stroke: { width:0 },
        dataLabels: { enabled:false },
        legend: { show:false },
        plotOptions: { pie: { donut: { size:'72%', labels: { show:true, name:{show:true,fontSize:'12px',color:'#98a6ad',offsetY:18}, value:{show:true,fontSize:'22px',color:'#313a46',fontWeight:600,offsetY:-10}, total:{show:true,label:'Total SRs',formatter:()=>'1,284'} } } } },
    }).render();

    // Warranty Chart
    new ApexCharts(document.querySelector("#warrantyChart"), {
        chart: { type:'bar', height:230, toolbar:{show:false}, fontFamily:'Roboto, sans-serif' },
        series: [
            { name:'In Warranty',    data:[62,71,68,75,82,78] },
            { name:'Out of Warranty',data:[38,29,32,25,18,22] },
        ],
        colors: ['#0acf97','#ffbc00'],
        plotOptions: { bar:{horizontal:false,columnWidth:'55%',borderRadius:3} },
        stroke: { width:1, colors:['transparent'] },
        xaxis: { categories:['Jan','Feb','Mar','Apr','May','Jun'], labels:{style:{fontSize:'11px',colors:'#98a6ad'}}, axisBorder:{show:false}, axisTicks:{show:false} },
        yaxis: { labels: { style:{fontSize:'11px',colors:'#98a6ad'}, formatter:(v)=>v+'%' } },
        grid: { borderColor:'#eef2f7', strokeDashArray:3 },
        legend: { position:'top', horizontalAlign:'right', fontSize:'12px', markers:{width:9,height:9,radius:9} },
        dataLabels: { enabled:false },
        tooltip: { theme:'light' },
    }).render();
</script>
@endpush