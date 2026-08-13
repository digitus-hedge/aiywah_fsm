<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user()->loadMissing('role');

        $logs = NotificationLog::with('causer')
            ->visibleTo($user)
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'unread' => NotificationLog::visibleTo($user)->unread()->count(),
            'logs' => $logs->map(fn($n) => [
                'id'      => $n->id,
                'sr_id'   => $n->service_request_id,
                'event'   => $n->event,
                'title'   => $n->title,
                'message' => $n->message,
                'from'    => $n->from_status,
                'to'      => $n->to_status,
                'by'      => optional($n->causer)->name,
                'ago'     => $n->created_at->diffForHumans(),
                'read'    => (bool) $n->read_at,
            ]),
        ]);
    }

    public function markRead(Request $request)
    {
        $user = auth()->user()->loadMissing('role');

        // mark one, or all if no id given
        if ($request->filled('id')) {
            NotificationLog::visibleTo($user)
                ->whereKey($request->id)
                ->update(['read_at' => now()]);
        } else {
            NotificationLog::visibleTo($user)
                ->unread()
                ->update(['read_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }

    public function all(Request $request)
    {
        $user = auth()->user()->loadMissing('role');

        $query = NotificationLog::with(['causer', 'serviceRequest'])
            ->visibleTo($user)
            ->latest();

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