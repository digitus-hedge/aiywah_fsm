<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest('id');

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

        $logs    = $query->paginate(25)->withQueryString();
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
        $activityLog->load('user');

        return response()->json([
            'status' => true,
            'data'   => array_merge($activityLog->toArray(), [
                'created_at_human' => $activityLog->created_at?->format('d M Y · h:i A'),
            ]),
        ]);
    }
}