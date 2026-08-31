{{--
|--------------------------------------------------------------------------
| Accountant Dashboard
|--------------------------------------------------------------------------
| resources/views/accountant/dashboard.blade.php
|
| Deliberately small: this role only has Invoice Panel and Expense Ledger
| permissions, so that's all this page shows. Same colour theme and card
| language as admin/dashboard.blade.php, no KPI row, no charts.
--}}

@extends('layouts.layout')

@section('title', 'Accountant Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
footer.footer { display: none; }

#accDash{
  --card:#fff; --card2:#faf9f7;
  --border:rgba(0,0,0,.07);
  --shadow:0 2px 20px rgba(0,0,0,.06);
  --text:#1a1614; --muted:#8a8480; --light:#bbb8b4;
  --gold:#9a8053; --gold2:#b8975e; --gold-bg:rgba(154,128,83,.1);
  --ok:#15803d; --danger:#dc2626;

  font-family:var(--font-header);
  font-size:.875rem;
  color:var(--text);
}
[data-theme="dark"] #accDash{
  --card:#1e1b18; --card2:#252220;
  --border:rgba(255,255,255,.07);
  --shadow:0 2px 20px rgba(0,0,0,.4);
  --text:#e8e0d4; --muted:#7a756e; --light:#4a4540;
  --gold-bg:rgba(154,128,83,.12);
}

#accDash *,#accDash *::before,#accDash *::after{box-sizing:border-box;}
#accDash a{text-decoration:none;}
#accDash .greeting,#accDash .c-label{font-family:var(--font-body);letter-spacing:-.01em;}

/* ── HEADER / GREETING ── */
#accDash .greeting-row{display:flex;align-items:flex-start;justify-content:space-between;
  gap:20px;margin-bottom:22px;flex-wrap:wrap;}
#accDash .greeting{font-size:clamp(1.2rem,4vw,1.5rem);font-weight:700;color:var(--text);margin-bottom:3px;}
#accDash .greeting-sub{font-size:.82rem;color:var(--muted);}

/* ── FILTER BAR ── */
#accDash .filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
#accDash .fq-pill{
  display:inline-flex;align-items:center;justify-content:center;
  height:32px;padding:0 14px;box-sizing:border-box;
  border:1px solid var(--border);border-radius:20px;
  background:var(--card);color:var(--muted);
  font-size:.76rem;font-weight:500;white-space:nowrap;cursor:pointer;
  transition:background .15s,border-color .15s,color .15s;
}
#accDash .fq-pill:hover{border-color:var(--gold);}
#accDash .fq-pill.active{border-color:var(--gold);background:var(--gold-bg);color:var(--gold);font-weight:600;}

#accDash .f-daterange{
  display:inline-flex;align-items:center;gap:8px;
  height:32px;padding:0 14px;box-sizing:border-box;min-width:0;
  border:1px solid var(--border);border-radius:20px;
  background:var(--card);color:var(--muted);
  transition:border-color .15s,background .15s;
}
#accDash .f-daterange.active{border-color:var(--gold);background:var(--gold-bg);color:var(--gold);}
#accDash .f-daterange input[type="date"]{
  -webkit-appearance:none;appearance:none;border:0!important;outline:0;
  background:transparent!important;box-shadow:none!important;color:inherit;
  font-size:.76rem;font-weight:500;line-height:1;padding:0;margin:0;
  width:auto;min-width:0;height:100%;cursor:pointer;
}
#accDash .f-date-lbl{font-size:.76rem;font-weight:500;color:inherit;flex-shrink:0;line-height:1;}

#accDash .fq-reset{
  display:inline-flex;align-items:center;justify-content:center;gap:5px;
  height:32px;padding:0 14px;box-sizing:border-box;
  border-radius:20px;border:1px solid rgba(220,38,38,.22);
  background:rgba(220,38,38,.07);color:#dc2626;
  font-size:.76rem;font-weight:500;white-space:nowrap;
  text-decoration:none;cursor:pointer;
  transition:background .15s,border-color .15s;
}
#accDash .fq-reset:hover{background:rgba(220,38,38,.14);border-color:rgba(220,38,38,.35);}

/* ── CARDS ── */
#accDash .card{
  background:var(--card);border:1px solid var(--border);border-radius:14px;
  box-shadow:var(--shadow);padding:20px;margin-bottom:18px;
}
#accDash .c-hdr{display:flex;align-items:center;justify-content:space-between;
  gap:12px;margin-bottom:14px;flex-wrap:wrap;}
#accDash .c-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
  color:var(--muted);display:flex;align-items:center;gap:7px;}
#accDash .c-label i{color:var(--gold);font-size:.9rem;}
#accDash .c-sub{font-size:.76rem;color:var(--muted);}
#accDash .c-sub strong{color:var(--text);font-weight:700;}

/* ── TABLE ── */
#accDash .tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}
#accDash .t-tbl{width:100%;min-width:640px;border-collapse:collapse;}
#accDash .t-tbl thead th{padding:9px 14px;font-size:.67rem;font-weight:700;text-align:left;
  text-transform:uppercase;letter-spacing:.07em;color:var(--muted);
  background:var(--card2);border-bottom:1px solid var(--border);white-space:nowrap;}
#accDash .t-tbl tbody tr{border-bottom:1px solid var(--border);}
#accDash .t-tbl tbody tr:last-child{border-bottom:none;}
#accDash .t-tbl tbody tr:hover{background:var(--gold-bg);}
#accDash .t-tbl td{padding:10px 14px;font-size:.79rem;vertical-align:middle;white-space:nowrap;}
#accDash .t-tbl td.wrap{white-space:normal;}
#accDash .t-ref{font-weight:700;color:var(--gold);font-family:var(--font-body);}
#accDash .t-amt{font-weight:700;color:var(--text);font-variant-numeric:tabular-nums;}
#accDash .t-pill{font-size:.67rem;font-weight:700;padding:2px 8px;border-radius:8px;
  display:inline-flex;align-items:center;gap:3px;background:var(--gold-bg);color:var(--gold);}

/* ── EMPTY STATE ── */
#accDash .empty{padding:26px 14px;text-align:center;color:var(--muted);font-size:.8rem;}
#accDash .empty i{display:block;font-size:1.3rem;color:var(--light);margin-bottom:6px;}

/* ── SR BREAKDOWN (pie) ── */
#accDash .brk-wrap{display:flex;align-items:center;gap:22px;flex-wrap:wrap;}
#accDash .brk-chart{position:relative;width:150px;height:150px;flex-shrink:0;}
#accDash .brk-chart canvas{position:absolute;inset:0;width:100%!important;height:100%!important;}
#accDash .brk-total{position:absolute;inset:0;display:flex;flex-direction:column;
  align-items:center;justify-content:center;pointer-events:none;}
#accDash .brk-total-n{font-family:var(--font-body);font-size:1.5rem;font-weight:700;color:var(--text);line-height:1;}
#accDash .brk-total-lbl{font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-top:2px;}
#accDash .brk-legend{display:flex;flex-direction:column;gap:9px;flex:1;min-width:170px;}
#accDash .brk-row{display:flex;align-items:center;gap:9px;font-size:.79rem;}
#accDash .brk-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0;}
#accDash .brk-lbl{flex:1;color:var(--muted);}
#accDash .brk-n{font-weight:700;color:var(--text);font-variant-numeric:tabular-nums;}
@media (max-width:480px){
  #accDash .brk-wrap{justify-content:center;}
  #accDash .brk-legend{min-width:0;width:100%;}
}

/* ── RESPONSIVE ── */
@media (max-width:700px){
  #accDash .greeting-row{flex-direction:column;gap:14px;}
  #accDash .filter-bar{width:100%;overflow-x:auto;flex-wrap:nowrap;padding-bottom:4px;}
  #accDash .filter-bar > *{flex:0 0 auto;}
}
@media (max-width:560px){
  #accDash .card{padding:16px;}
}
</style>
@endpush

@section('content')
@php
    $filters        = $filters        ?? [];
    $invoiceItems   = $invoiceItems   ?? [];
    $expenseItems   = $expenseItems   ?? [];
    $quotationItems = $quotationItems ?? [];
@endphp

<div id="accDash">

    {{-- ── GREETING + FILTERS ─────────────────────────────────────────── --}}
    <div class="greeting-row">
        <div>
            <div class="greeting">{{ $greeting ?? 'Accountant Dashboard' }}</div>
            <div class="greeting-sub">
                <i class="bi bi-calendar3" style="color:var(--gold);margin-right:4px;"></i>
                {{ $today ?? now()->format('l, d F Y') }}
                @isset($greetingSub)
                    &nbsp;·&nbsp; {{ $greetingSub }}
                @endisset
            </div>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="filter-bar" id="accFilters">
            <input type="hidden" name="range" id="rangeField" value="{{ $filters['range'] ?? 'month' }}">

            <button type="button" onclick="setRange(this,'today')"
                    class="fq-pill {{ ($filters['range'] ?? null) === 'today' ? 'active' : '' }}">Today</button>
            <button type="button" onclick="setRange(this,'month')"
                    class="fq-pill {{ ($filters['range'] ?? null) === 'month' ? 'active' : '' }}">This Month</button>
            <button type="button" onclick="setRange(this,'quarter')"
                    class="fq-pill {{ ($filters['range'] ?? null) === 'quarter' ? 'active' : '' }}">This Quarter</button>

            <div class="f-daterange {{ ($filters['range'] ?? null) === 'custom' ? 'active' : '' }}">
                <span class="f-date-lbl">From</span>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                    max="{{ now()->toDateString() }}" onchange="applyCustomRange(this)" aria-label="From date">
            </div>
            <div class="f-daterange {{ ($filters['range'] ?? null) === 'custom' ? 'active' : '' }}">
                <span class="f-date-lbl">To</span>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                    max="{{ now()->toDateString() }}" onchange="applyCustomRange(this)" aria-label="To date">
            </div>

            @if (($filters['range'] ?? 'month') !== 'month' || !empty($filters['from']) || !empty($filters['to']))
                <a href="{{ url()->current() }}" class="fq-reset" title="Clear filters">
                    <i class="bi bi-arrow-counterclockwise"></i>Reset
                </a>
            @endif
        </form>
    </div>

    {{-- ── SR BREAKDOWN (WARRANTY / QUOTE STATUS) ─────────────────────── --}}
    @php $brk = $srBreakdown ?? ['rows' => [], 'total' => 0]; @endphp
    <div class="card">
        <div class="c-hdr">
            <div class="c-label"><i class="bi bi-pie-chart"></i>SR Breakdown</div>
            <div class="c-sub">{{ $rangeLabel ?? 'this month' }}</div>
        </div>

        @if (($brk['total'] ?? 0) > 0)
            <div class="brk-wrap">
                <div class="brk-chart">
                    <canvas id="accBreakdownChart"></canvas>
                    <div class="brk-total">
                        <span class="brk-total-n">{{ $brk['total'] }}</span>
                        <span class="brk-total-lbl">SRs</span>
                    </div>
                </div>
                <div class="brk-legend">
                    @foreach ($brk['rows'] as $row)
                        <div class="brk-row">
                            <span class="brk-dot" style="background:{{ data_get($row, 'color') }};"></span>
                            <span class="brk-lbl">{{ data_get($row, 'label') }}</span>
                            <span class="brk-n">{{ data_get($row, 'count') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="empty"><i class="bi bi-pie-chart"></i>No service requests logged in this period.</p>
        @endif
    </div>

    {{-- ── QUOTATION DESK ─────────────────────────────────────────────── --}}
    <div class="card">
        <div class="c-hdr">
            <div class="c-label"><i class="bi bi-file-earmark-text"></i>Quotation Desk</div>
            <div class="c-sub">
                <strong>{{ $quotationCount ?? 0 }}</strong> {{ \Illuminate\Support\Str::plural('SR', $quotationCount ?? 0) }}
                pending to quote &nbsp;·&nbsp; {{ $rangeLabel ?? 'this month' }}
            </div>
        </div>

        <div class="tbl-scroll">
            <table class="t-tbl">
                <thead>
                    <tr>
                        <th>SR Reference</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Raised</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotationItems as $row)
                        <tr>
                            <td class="t-ref">{{ data_get($row, 'ref') }}</td>
                            <td class="wrap">{{ data_get($row, 'client') }}</td>
                            <td>{{ data_get($row, 'category') }}</td>
                            <td><span class="t-pill">{{ data_get($row, 'status') }}</span></td>
                            <td>{{ data_get($row, 'date') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><p class="empty"><i class="bi bi-file-earmark-text"></i>No SRs pending quotation in this period.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── INVOICE PANEL ──────────────────────────────────────────────── --}}
    <div class="card">
        <div class="c-hdr">
            <div class="c-label"><i class="bi bi-receipt"></i>Invoice Panel</div>
            <div class="c-sub">
                <strong>{{ $invoiceCount ?? 0 }}</strong> {{ \Illuminate\Support\Str::plural('invoice', $invoiceCount ?? 0) }}
                &nbsp;·&nbsp; <strong>{{ $invoiceTotalFormatted ?? '—' }}</strong> total, {{ $rangeLabel ?? 'this month' }}
            </div>
        </div>

        <div class="tbl-scroll">
            <table class="t-tbl">
                <thead>
                    <tr>
                        <th>SR Reference</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoiceItems as $row)
                        <tr>
                            <td class="t-ref">{{ data_get($row, 'ref') }}</td>
                            <td class="wrap">{{ data_get($row, 'client') }}</td>
                            <td>{{ data_get($row, 'category') }}</td>
                            <td><span class="t-pill">{{ data_get($row, 'status') }}</span></td>
                            <td>{{ data_get($row, 'date') }}</td>
                            <td class="t-amt" style="text-align:right;">{{ data_get($row, 'amount') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><p class="empty"><i class="bi bi-receipt"></i>No invoices submitted in this period.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── EXPENSE LEDGER ─────────────────────────────────────────────── --}}
    <div class="card">
        <div class="c-hdr">
            <div class="c-label"><i class="bi bi-wallet2"></i>Expense Ledger</div>
            <div class="c-sub">
                <strong>{{ $expenseCount ?? 0 }}</strong> {{ \Illuminate\Support\Str::plural('entry', $expenseCount ?? 0) }}
                &nbsp;·&nbsp; <strong>{{ $expenseTotalFormatted ?? '—' }}</strong> total, {{ $rangeLabel ?? 'this month' }}
            </div>
        </div>

        <div class="tbl-scroll">
            <table class="t-tbl">
                <thead>
                    <tr>
                        <th>SR Reference</th>
                        <th>Client</th>
                        <th>Technician</th>
                        <th>Date</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenseItems as $row)
                        <tr>
                            <td class="t-ref">{{ data_get($row, 'ref') }}</td>
                            <td class="wrap">{{ data_get($row, 'client') }}</td>
                            <td>{{ data_get($row, 'tech') }}</td>
                            <td>{{ data_get($row, 'date') }}</td>
                            <td class="t-amt" style="text-align:right;">{{ data_get($row, 'amount') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><p class="empty"><i class="bi bi-wallet2"></i>No field expenses logged in this period.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    window.ACC_BREAKDOWN = @json($srBreakdown ?? null);

    (function () {
        var canvas = document.getElementById('accBreakdownChart');
        if (!canvas || typeof Chart === 'undefined') return;
        var data = window.ACC_BREAKDOWN;
        if (!data || !data.total) return;

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: data.colors,
                    borderWidth: 2,
                    borderColor: getComputedStyle(document.getElementById('accDash')).getPropertyValue('--card') || '#fff',
                    hoverOffset: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,15,15,.9)',
                        cornerRadius: 8,
                        padding: 10,
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,.75)',
                    },
                },
            },
        });
    })();

    function accForm(el) { return el.closest('form'); }

    function setRange(el, val) {
        var f = accForm(el);
        f.querySelector('input[name="range"]').value = val;
        var from = f.querySelector('input[name="from"]');
        var to   = f.querySelector('input[name="to"]');
        if (from) from.value = '';
        if (to)   to.value   = '';
        f.submit();
    }

    function applyCustomRange(el) {
        var f      = accForm(el);
        var fromEl = f.querySelector('input[name="from"]');
        var toEl   = f.querySelector('input[name="to"]');

        if (!fromEl.value || !toEl.value) return;

        if (fromEl.value > toEl.value) {
            var swap = fromEl.value;
            fromEl.value = toEl.value;
            toEl.value   = swap;
        }

        f.querySelector('input[name="range"]').value = 'custom';
        f.submit();
    }
</script>
@endpush

{{--
|--------------------------------------------------------------------------
| Controller contract
|--------------------------------------------------------------------------
| return view('accountant.dashboard', [
|     'greeting'    => 'Good morning, '.auth()->user()->first_name.'!',
|     'greetingSub' => "Here's your finance overview",
|     'today'       => now()->format('l, d F Y'),
|
|     'filters'    => ['range' => 'month', 'from' => null, 'to' => null],
|     'rangeLabel' => 'this month',
|
|     'srBreakdown' => [
|         'labels' => ['In Warranty', 'Out of Warranty', 'Quoted', 'Pending to Quote'],
|         'values' => [18, 9, 6, 4],
|         'colors' => ['#9a8053', '#393837', '#15803d', '#d97706'],
|         'rows'   => [
|             ['label' => 'In Warranty', 'count' => 18, 'color' => '#9a8053'],
|         ],
|         'total' => 27,
|     ],
|
|     'quotationItems' => [
|         ['ref' => 'SR-2026-00052', 'client' => 'Acme LLC', 'category' => 'Electrical',
|          'status' => 'Pending', 'date' => '14 Aug 2026'],
|     ],
|     'quotationCount' => 6,
|
|     'invoiceItems' => [
|         ['ref' => 'SR-2026-00041', 'client' => 'Acme LLC', 'category' => 'Electrical',
|          'status' => 'Completed', 'date' => '12 Aug 2026', 'amount' => 'AED 1,240.00'],
|     ],
|     'invoiceCount'          => 41,
|     'invoiceTotalFormatted' => 'AED 38.4k',
|
|     'expenseItems' => [
|         ['ref' => 'SR-2026-00041', 'client' => 'Acme LLC', 'tech' => 'M. Khan',
|          'date' => '11 Aug 2026', 'amount' => 'AED 340.00'],
|     ],
|     'expenseCount'          => 58,
|     'expenseTotalFormatted' => 'AED 14.8k',
| ]);
--}}