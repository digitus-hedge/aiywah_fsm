<?php

namespace App\Http\Controllers;

use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WhatsappLogController extends Controller
{
    public function __construct(private WhatsAppService $wa) {}

    public function index(Request $request)
    {
        $logs = WhatsappLog::query()
            ->search($request->input('sr'))
            ->event($request->input('event'))
            ->status($request->input('status'))
            ->dateFrom($request->input('date_from'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'delivered' => WhatsappLog::whereIn('status', [WhatsappLog::STATUS_DELIVERED, WhatsappLog::STATUS_SENT])
                ->whereDate('created_at', today())->count(),
            'pending'   => WhatsappLog::where('status', WhatsappLog::STATUS_PENDING)->count(),
            'failed'    => WhatsappLog::where('status', WhatsappLog::STATUS_FAILED)->count(),
            'total'     => WhatsappLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        $rows = $logs->getCollection()->map->toRowArray()->values();

        $events = WhatsappLog::select('event')->distinct()->orderBy('event')->pluck('event');

        if ($request->ajax()) {
            return response()->json([
                'logs'  => $rows,
                'stats' => $stats,
                'meta'  => [
                    'current_page' => $logs->currentPage(),
                    'last_page'    => $logs->lastPage(),
                    'total'        => $logs->total(),
                    'links'        => $logs->linkCollection(),
                ],
            ]);
        }

        return view('wa_notification_log', [
            'logs'          => $rows,
            'paginator'     => $logs,
            'stats'         => $stats,
            'events'        => $events,
            'totalThisMonth'=> $stats['total'],
        ]);
    }

    public function show(WhatsappLog $log)
    {
        return response()->json([
            'sr'        => $log->sr_reference,
            'recipient' => $log->recipient,
            'client'    => $log->client_name,
            'event'     => $log->event,
            'status'    => $log->status,
            'template'  => $log->template,
            'message'   => $log->message,
            'error'     => $log->error,
            'retries'   => $log->retry_count,
            'time'      => optional($log->created_at)->format('d M Y, h:i A'),
        ]);
    }

    public function retry(WhatsappLog $log)
    {
        if ($log->status !== WhatsappLog::STATUS_FAILED) {
            return response()->json(['ok' => false, 'message' => 'Only failed messages can be retried.'], 422);
        }

        $log = $this->wa->retryLog($log);

        return response()->json([
            'ok'      => $log->status !== WhatsappLog::STATUS_FAILED,
            'status'  => $log->status,
            'message' => $log->status === WhatsappLog::STATUS_FAILED
                ? ($log->error ?? 'Retry failed.')
                : "Message re-queued for {$log->sr_reference}",
        ]);
    }

    public function retryAll()
    {
        $failed = WhatsappLog::where('status', WhatsappLog::STATUS_FAILED)->get();
        foreach ($failed as $log) {
            $this->wa->retryLog($log);
        }

        return response()->json([
            'ok'      => true,
            'message' => $failed->count() . ' failed message(s) queued for retry.',
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = WhatsappLog::query()
            ->search($request->input('sr'))
            ->event($request->input('event'))
            ->status($request->input('status'))
            ->dateFrom($request->input('date_from'))
            ->latest();

        $filename = 'whatsapp-log-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['SR ID', 'Recipient', 'Client', 'Event', 'Status', 'Message', 'Error', 'Retries', 'Timestamp']);
            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $l) {
                    fputcsv($out, [
                        $l->sr_reference, $l->recipient, $l->client_name, $l->event,
                        $l->status, $l->message, $l->error, $l->retry_count,
                        optional($l->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}