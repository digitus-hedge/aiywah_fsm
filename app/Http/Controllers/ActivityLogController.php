<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'subject'])->latest('id');

        if ($request->filled('module'))    $query->where('module', $request->module);
        if ($request->filled('type'))      $query->where('activity_type', $request->type);
        if ($request->filled('user_id'))   $query->where('user_id', $request->user_id);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(fn($q) => $q->where('description', 'like', "%{$s}%")
                ->orWhere('subject_id', $s));
        }

        $logs = $query->paginate(25)->withQueryString();

        // Attach a human-readable record label to each row without touching the DB again.
        $logs->getCollection()->transform(function ($log) {
            $log->record_label = $this->recordLabel($log);
            $log->is_sr         = class_basename($log->subject_type ?? '') === 'ServiceRequest';
            return $log;
        });

        $modules = ActivityLog::whereNotNull('module')->distinct()->orderBy('module')->pluck('module');
        $types   = ActivityLog::whereNotNull('activity_type')->distinct()->orderBy('activity_type')->pluck('activity_type');
        $users   = User::orderBy('name')->get(['id', 'name']);

        $today = today();
        $stats = [
            'total'   => ActivityLog::count(),
            'created' => ActivityLog::whereDate('created_at', $today)->where('activity_type', 'created')->count(),
            'updated' => ActivityLog::whereDate('created_at', $today)->where('activity_type', 'updated')->count(),
            'deleted' => ActivityLog::whereDate('created_at', $today)->where('activity_type', 'deleted')->count(),
        ];

        return view('activity_log', compact('logs', 'modules', 'types', 'users', 'stats'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user', 'subject']);

        return response()->json([
            'status' => true,
            'data'   => array_merge($activityLog->toArray(), [
                'created_at_human' => $activityLog->created_at?->format('d M Y · h:i A'),
                'record_label'     => $this->recordLabel($activityLog),
                'is_sr'            => class_basename($activityLog->subject_type ?? '') === 'ServiceRequest',
            ]),
        ]);
    }

/**
 * Human-readable record reference — SR-2026-00085 for service requests,
 * the unique code for clients/projects, or a fallback #id for anything else.
 * Falls back to the logged old/new value snapshot when the live record
 * has since been deleted (subject relation resolves to null).
 */
private function recordLabel(ActivityLog $log): string
{
    if (! $log->subject_id) {
        return '—';
    }

    $type = class_basename($log->subject_type ?? '');
    $sub  = $log->subject; // null if hard-deleted since this log entry was written

    // Snapshot fallback — decode whichever value set is present.
    $snapshot = $this->decodeValues($log->new_values) ?? $this->decodeValues($log->old_values) ?? [];

    return match ($type) {
        'ServiceRequest' => 'SR-'
            . ($sub->created_at?->year ?? $log->created_at?->year ?? now()->year)
            . '-' . str_pad((string) $log->subject_id, 5, '0', STR_PAD_LEFT),

        'Client' => $sub->unique_code
            ?? ($snapshot['unique_code'] ?? null)
            ?? '#' . $log->subject_id,

        'Project' => $sub->project_code
            ?? ($snapshot['project_code'] ?? null)
            ?? '#' . $log->subject_id,

        'User' => $sub->name
            ?? ($snapshot['name'] ?? null)
            ?? '#' . $log->subject_id,

        default => '#' . $log->subject_id,
    };
}

/** Decode a stored values column, whether it's already an array/cast or a raw JSON string. */
private function decodeValues($value): ?array
{
    if (is_array($value)) {
        return $value;
    }

    if (is_string($value) && $value !== '') {
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }

    return null;
}
}