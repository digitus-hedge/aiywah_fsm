<?php

namespace App\Http\Controllers;

use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WhatsappLogController extends Controller
{
    public function __construct(private WhatsAppService $wa) {}

    public function index(Request $request)
    {
        $user   = auth()->user();
        $access = $this->waAccessLevel($user);

        // 'no' → the permission matrix hasn't granted this role the page at all
        abort_unless(in_array($access, ['yes', 'rls'], true), 403);

        $scope = fn($q) => $q->when($access === 'rls', fn($x) =>
            $x->whereHas('serviceRequest', function ($sr) use ($user) {
                $sr->where('assigned_user_id', $user->id)   // technician on the job
                    ->orWhere('assigned_se', $user->id)      // SE who owns the ticket
                    ->orWhere('created_by', $user->id);      // FD who logged it
            })
        );

        $logs = WhatsappLog::query()
                ->tap($scope)
                ->search($request->input('sr'))
                ->event($request->input('event'))
                ->status($request->input('status'))
                ->dateFrom($request->input('date_from'))
                ->dateTo($request->input('date_to'))
                ->latest()
                ->paginate(25)
                ->withQueryString();
        $stats = [
            'delivered' => WhatsappLog::tap($scope)
                ->whereIn('status', [WhatsappLog::STATUS_DELIVERED, WhatsappLog::STATUS_SENT])
                ->whereDate('created_at', today())->count(),
            'pending'   => WhatsappLog::tap($scope)->where('status', WhatsappLog::STATUS_PENDING)->count(),
            'failed'    => WhatsappLog::tap($scope)->where('status', WhatsappLog::STATUS_FAILED)->count(),
            'total'     => WhatsappLog::tap($scope)
                ->whereMonth('created_at', now()->month)
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
            'logs'           => $rows,
            'paginator'      => $logs,
            'stats'          => $stats,
            'events'         => $events,
            'totalThisMonth' => $stats['total'],
        ]);
    }

    /**
     * Reads the seeded access level for the wa_notification_log permission
     * on this user's role: 'yes' (full), 'rls' (own only), or 'no' (denied).
     * Keeps this controller in sync with PermissionSeeder without duplicating
     * the role-code checks it encodes.
     */
    private function waAccessLevel($user): string
    {
        if (!$user || !$user->role_id) {
            return 'no';
        }

        return DB::table('permission_role as pr')
            ->join('permissions as p', 'p.id', '=', 'pr.permission_id')
            ->where('pr.role_id', $user->role_id)
            ->where('p.key', 'wa_notification_log')
            ->value('pr.access') ?? 'no';
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
            ->dateTo($request->input('date_to'))
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