<?php

namespace App\Http\Controllers;

use App\Models\IdleEvent;
use App\Models\PresenceLog;
use App\Models\TimeSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminTimeSessionController extends Controller
{
    /**
     * GET /admin/time-sessions
     * Paginated list of all users' sessions with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TimeSession::with('user')
            ->latest('started_at');

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            );
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('started_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('started_at', '<=', $dateTo);
        }

        $sessions = $query->paginate(20);

        return response()->json(
            $sessions->through(fn ($s) => $this->formatSession($s))
        );
    }

    /**
     * GET /admin/time-sessions/{timeSession}
     * Full detail for a single session including presence log timeline and idle events.
     */
    public function show(TimeSession $timeSession): JsonResponse
    {
        $timeSession->load(['user', 'idleEvents']);

        // Presence logs have no session_id — retrieve by user + time window.
        $presenceLogs = PresenceLog::with('status')
            ->where('user_id', $timeSession->user_id)
            ->where('logged_at', '>=', $timeSession->started_at)
            ->when(
                $timeSession->ended_at,
                fn ($q) => $q->where('logged_at', '<=', $timeSession->ended_at)
            )
            ->orderBy('logged_at')
            ->get();

        $totalSeconds = $timeSession->ended_at
            ? (int) $timeSession->started_at->diffInSeconds($timeSession->ended_at)
            : null;

        $workSeconds = $totalSeconds !== null
            ? max(0, $totalSeconds - $timeSession->total_idle_seconds)
            : null;

        return response()->json([
            'id'                  => $timeSession->id,
            'user'                => [
                'id'    => $timeSession->user->id,
                'name'  => $timeSession->user->name,
                'email' => $timeSession->user->email,
            ],
            'started_at'          => $timeSession->started_at,
            'ended_at'            => $timeSession->ended_at,
            'is_active'           => is_null($timeSession->ended_at),
            'total_idle_seconds'  => $timeSession->total_idle_seconds,
            'total_seconds'       => $totalSeconds,
            'work_seconds'        => $workSeconds,
            'presence_logs'       => $presenceLogs->map(fn ($log) => [
                'id'        => $log->id,
                'logged_at' => $log->logged_at,
                'status'    => [
                    'name'  => $log->status?->name,
                    'code'  => $log->status?->code,
                    'color' => $log->status?->color,
                ],
            ]),
            'idle_events'         => $timeSession->idleEvents->map(fn ($e) => [
                'id'               => $e->id,
                'idle_start'       => $e->idle_start,
                'idle_end'         => $e->idle_end,
                'duration_seconds' => $e->idle_end
                    ? (int) \Carbon\Carbon::parse($e->idle_start)->diffInSeconds($e->idle_end)
                    : null,
            ]),
        ]);
    }

    /**
     * GET /admin/time-sessions/stats
     * KPI summary: active sessions, sessions today, net hours today, idle events today.
     */
    public function stats(): JsonResponse
    {
        $today = now()->toDateString();

        $activeSessions  = TimeSession::whereNull('ended_at')->count();
        $sessionsToday   = TimeSession::whereDate('started_at', $today)->count();
        $idleEventsToday = IdleEvent::whereDate('idle_start', $today)->count();

        $workSecondsToday = TimeSession::whereDate('started_at', $today)
            ->whereNotNull('ended_at')
            ->get()
            ->sum(fn ($s) => max(0, (int) $s->started_at->diffInSeconds($s->ended_at) - $s->total_idle_seconds));

        return response()->json([
            'active_sessions'         => $activeSessions,
            'sessions_today'          => $sessionsToday,
            'work_seconds_today'      => $workSecondsToday,
            'idle_events_today'       => $idleEventsToday,
        ]);
    }

    /**
     * GET /admin/time-sessions/export?date_from=YYYY-MM-DD&date_to=YYYY-MM-DD
     * Full session list for Excel export — includes idle events, no pagination.
     */
    public function export(Request $request): JsonResponse
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());

        $sessions = TimeSession::with(['user', 'idleEvents'])
            ->whereDate('started_at', '>=', $dateFrom)
            ->whereDate('started_at', '<=', $dateTo)
            ->orderBy('user_id')
            ->orderBy('started_at')
            ->get();

        return response()->json($sessions->map(function ($s) {
            $workSeconds = $s->ended_at
                ? max(0, (int) $s->started_at->diffInSeconds($s->ended_at) - $s->total_idle_seconds)
                : null;

            return [
                'user_id'            => $s->user_id,
                'user_name'          => $s->user->name,
                'date'               => $s->started_at->toDateString(),
                'started_at'         => $s->started_at->toIso8601String(),
                'ended_at'           => $s->ended_at?->toIso8601String(),
                'total_idle_seconds' => $s->total_idle_seconds,
                'work_seconds'       => $workSeconds,
                'idle_events'        => $s->idleEvents
                    ->sortBy('idle_start')
                    ->values()
                    ->map(fn ($e) => [
                        'idle_start' => $e->idle_start,
                        'idle_end'   => $e->idle_end,
                    ]),
            ];
        }));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function formatSession(TimeSession $s): array
    {
        $totalSeconds = $s->ended_at
            ? (int) $s->started_at->diffInSeconds($s->ended_at)
            : null;

        $workSeconds = $totalSeconds !== null
            ? max(0, $totalSeconds - $s->total_idle_seconds)
            : null;

        return [
            'id'                 => $s->id,
            'user'               => [
                'id'    => $s->user->id,
                'name'  => $s->user->name,
                'email' => $s->user->email,
            ],
            'started_at'         => $s->started_at,
            'ended_at'           => $s->ended_at,
            'is_active'          => is_null($s->ended_at),
            'total_idle_seconds' => $s->total_idle_seconds,
            'total_seconds'      => $totalSeconds,
            'work_seconds'       => $workSeconds,
        ];
    }
}
