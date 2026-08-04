<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ $project->project_code }} — Service report</title>
<style>
  /* dompdf: table-based layout only, no flex/grid, no webfonts */
  @page{margin:26mm 16mm 20mm}
  body{font-family:DejaVu Sans, sans-serif;font-size:10.5px;color:#101a20;line-height:1.5}
  h1{font-size:18px;margin:0 0 2px}
  h2{font-size:12.5px;margin:22px 0 8px;padding-bottom:4px;border-bottom:1px solid #e3e8eb}
  .muted{color:#7b8a94}
  .rule{border-bottom:2px solid #101a20;margin:10px 0 16px}
  table{width:100%;border-collapse:collapse}
  td{vertical-align:top;padding:3px 0}
  .kv td:first-child{width:34%;color:#7b8a94}
  .chip{display:inline-block;padding:2px 8px;border-radius:9px;font-size:9.5px;font-weight:bold;color:#fff}
  .job{border:1px solid #e3e8eb;border-radius:6px;padding:12px 14px;margin-bottom:12px;page-break-inside:avoid}
  .ref{font-size:9.5px;color:#7b8a94;letter-spacing:.08em}
  .photos td{width:50%;padding:4px}
  .photos img{width:100%;height:auto;border:1px solid #e3e8eb;border-radius:4px}
  .cap{font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:#7b8a94;padding-bottom:3px}
  .step{padding:2px 0}
  .dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:#0e7c86;margin-right:7px}
  .dot.off{background:#d7dee2}
  .foot{margin-top:26px;padding-top:8px;border-top:1px solid #e3e8eb;font-size:9px;color:#7b8a94}
</style>
</head>
<body>

@if(!empty($printFallback))
  <p style="background:#fdf1e3;padding:8px 10px;border-radius:6px">
    Use your browser's print dialog and choose "Save as PDF".
  </p>
@endif

<h1>{{ $project->project_name }}</h1>
<div class="muted">{{ $client->company_name }} · {{ $project->project_code }}</div>
<div class="rule"></div>

<table class="kv">
  <tr><td>Site</td><td>{{ $project->site_name }}{{ $project->site_address ? ', '.$project->site_address : '' }}</td></tr>
  <tr><td>Handover date</td><td>{{ $project->completion_date ? \Carbon\Carbon::parse($project->completion_date)->format('d M Y') : '—' }}</td></tr>
  <tr><td>Warranty</td><td>
    {{ $warrantyEnd ? ($inWarranty ? 'Active until '.$warrantyEnd->format('d M Y') : 'Ended '.$warrantyEnd->format('d M Y')) : 'Not recorded' }}
  </td></tr>
  <tr><td>Service visits</td><td>{{ $cards->count() }}</td></tr>
  <tr><td>Report generated</td><td>{{ $generatedAt->format('d M Y, h:i A') }}</td></tr>
</table>

<h2>Service history</h2>

@forelse($cards as $c)
  <div class="job">
    <table>
      <tr>
        <td><span class="ref">{{ $c['ref'] }} · {{ $c['logged'] }}</span><br>
            <strong>{{ $c['category'] }}</strong></td>
        <td style="text-align:right">
          <span class="chip" style="background:{{ $c['color'] }}">{{ $c['label'] }}</span>
        </td>
      </tr>
    </table>

    <table class="kv" style="margin-top:8px">
      <tr><td>Reported issue</td><td>{{ $c['issue'] ?: '—' }}</td></tr>
      <tr><td>Technician</td><td>{{ $c['technician'] ?: '—' }}</td></tr>
      <tr><td>Cost</td><td>{{ $c['coverage'] }}</td></tr>
    </table>

    @foreach($c['punches'] as $pn)
      @if($pn['work'] || $pn['summary'])
        <p style="margin:8px 0 4px">
          @if($pn['work']){{ $pn['work'] }}@endif
          @if($pn['summary']) {{ $pn['summary'] }}@endif
        </p>
      @endif
      @if($pn['items'])
        <p class="muted" style="margin:0">Parts: {{ collect($pn['items'])->map(fn($i) => $i['name'].' x'.$i['qty'])->join(', ') }}</p>
      @endif
    @endforeach

    @foreach($c['photos'] as $set)
      <table class="photos" style="margin-top:10px">
        <tr>
          <td class="cap">{{ $set['visit'] }} — before</td>
          <td class="cap">{{ $set['visit'] }} — after</td>
        </tr>
        <tr>
          <td>@if($set['before'] && file_exists($set['before']))<img src="{{ $set['before'] }}" alt="">@else<span class="muted">Not captured</span>@endif</td>
          <td>@if($set['after'] && file_exists($set['after']))<img src="{{ $set['after'] }}" alt="">@else<span class="muted">Not captured</span>@endif</td>
        </tr>
      </table>
    @endforeach

    <table style="margin-top:10px">
      @foreach($c['timeline'] as $s)
        <tr class="step">
          <td style="width:45%"><span class="dot {{ $s['done'] ? '' : 'off' }}"></span>{{ $s['label'] }}</td>
          <td class="muted">{{ $s['time'] ?: 'Pending' }}</td>
        </tr>
      @endforeach
    </table>
  </div>
@empty
  <p class="muted">No service visits recorded for this project.</p>
@endforelse

<div class="foot">
  {{ $client->company_name }} · {{ $project->project_code }} · This report reflects records held at the time of generation.
</div>
</body>
</html>