<?php

namespace App\Http\Controllers;

use App\Models\PresenceLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PresenceLogController extends Controller
{
    /**
     * GET /presence-logs
     * Returns today's presence logs for the authenticated user, oldest first.
     */
    public function index(Request $request)
    {
        $logs = PresenceLog::with('status')
            ->where('user_id', $request->user()->id)
            ->where('logged_at', '>=', now()->subWeek()->startOfDay())
            ->orderBy('logged_at', 'asc')
            ->get();

        return response()->json($logs);
    }

    /**
     * POST /presence-logs
     * Logs a presence status change for the authenticated user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'status_id' => 'required|integer|exists:statuses,id',
            'logged_at' => 'required|date',
        ]);

        $log = PresenceLog::create([
            'user_id'   => $request->user()->id,
            'status_id' => $data['status_id'],
            'logged_at' => $data['logged_at'],
        ]);

        $log->load('status');

        // Fire notification to admins / users with time_tracker → notifications access
        $statusCode = $log->status?->code;
        if ($statusCode) {
            NotificationService::notifyPresenceEvent($request->user(), $statusCode);
        }

        return response()->json($log, 201);
    }
}
