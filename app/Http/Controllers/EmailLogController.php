<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmailLogController extends Controller
{
    public function __construct(private EmailService $mail) {}

    public function index(Request $request)
    {
        $user   = auth()->user();
        $access = $this->mailAccessLevel($user);

        abort_unless(in_array($access, ['yes', 'rls'], true), 403);

        $scope = fn ($q) => $q->when($access === 'rls', fn ($x) =>
            $x->whereHas('serviceRequest', function ($sr) use ($user) {
                $sr->where('assigned_user_id', $user->id)
                   ->orWhere('assigned_se', $user->id)
                   ->orWhere('created_by', $user->id);
            })
        );

        $logs = EmailLog::query()
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
            'delivered' => EmailLog::tap($scope)
                ->where('status', EmailLog::STATUS_SENT)
                ->whereDate('created_at', today())->count(),
            'pending'   => EmailLog::tap($scope)->where('status', EmailLog::STATUS_PENDING)->count(),
            'failed'    => EmailLog::tap($scope)->where('status', EmailLog::STATUS_FAILED)->count(),
            'total'     => EmailLog::tap($scope)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        $rows   = $logs->getCollection()->map->toRowArray()->values();
        $events = EmailLog::select('event')->distinct()->orderBy('event')->pluck('event');

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

        return view('email_notification_log', [
            'logs'           => $rows,
            'paginator'      => $logs,
            'stats'          => $stats,
            'events'         => $events,
            'totalThisMonth' => $stats['total'],
        ]);
    }

    private function mailAccessLevel($user): string
    {
        if (!$user || !$user->role_id) {
            return 'no';
        }

        return DB::table('permission_role as pr')
            ->join('permissions as p', 'p.id', '=', 'pr.permission_id')
            ->where('pr.role_id', $user->role_id)
            ->where('p.key', 'email_notification_log')
            ->value('pr.access') ?? 'no';
    }

    public function show(EmailLog $log)
    {
        return response()->json([
            'sr'        => $log->sr_reference,
            'recipient' => $log->recipient,
            'client'    => $log->client_name,
            'event'     => $log->event,
            'status'    => ucfirst($log->status),
            'subject'   => $log->subject,
            'message'   => $log->message,
            'error'     => $log->error,
            'retries'   => $log->retry_count,
            'time'      => optional($log->created_at)->format('d M Y, h:i A'),
        ]);
    }

    public function retry(EmailLog $log)
    {
        if ($log->status !== EmailLog::STATUS_FAILED) {
            return response()->json(['ok' => false, 'message' => 'Only failed emails can be retried.'], 422);
        }

        $log = $this->mail->retryLog($log);

        return response()->json([
            'ok'      => $log->status !== EmailLog::STATUS_FAILED,
            'status'  => $log->status,
            'message' => $log->status === EmailLog::STATUS_FAILED
                ? ($log->error ?? 'Retry failed.')
                : "Email re-sent to {$log->recipient}",
        ]);
    }

    public function retryAll()
    {
        $failed = EmailLog::where('status', EmailLog::STATUS_FAILED)->get();
        foreach ($failed as $log) {
            $this->mail->retryLog($log);
        }

        return response()->json([
            'ok'      => true,
            'message' => $failed->count() . ' failed email(s) queued for retry.',
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = EmailLog::query()
            ->search($request->input('sr'))
            ->event($request->input('event'))
            ->status($request->input('status'))
            ->dateFrom($request->input('date_from'))
            ->dateTo($request->input('date_to'))
            ->latest();

        $filename = 'email-log-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['SR ID', 'Recipient', 'Client', 'Event', 'Status', 'Subject', 'Error', 'Retries', 'Timestamp']);
            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $l) {
                    fputcsv($out, [
                        $l->sr_reference, $l->recipient, $l->client_name, $l->event,
                        $l->status, $l->subject, $l->error, $l->retry_count,
                        optional($l->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}