<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $logs = NotificationLog::with('causer')
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'unread' => NotificationLog::unread()->count(),
            'logs' => $logs->map(fn($n) => [
                'id'      => $n->id,
                'sr_id'   => $n->service_request_id,
                'event'   => $n->event,
                'title'   => $n->title,
                'message' => $n->message,
                'from'    => $n->from_status,   // <-- must be present
                'to'      => $n->to_status,     // <-- must be present
                'by'      => optional($n->causer)->name,
                'ago'     => $n->created_at->diffForHumans(),
                'read'    => (bool) $n->read_at,
            ]),
        ]);
    }

    public function markRead(Request $request)
    {
        // mark one, or all if no id given
        if ($request->filled('id')) {
            NotificationLog::whereKey($request->id)->update(['read_at' => now()]);
        } else {
            NotificationLog::unread()->update(['read_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }

    public function all(Request $request)
    {
        $query = NotificationLog::with(['causer', 'serviceRequest'])->latest();

        // optional filters
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('state')) {
            $request->state === 'unread'
                ? $query->whereNull('read_at')
                : $query->whereNotNull('read_at');
        }

        $logs = $query->paginate(10)->withQueryString();

        return view('notification_all', compact('logs'));
    }
}
