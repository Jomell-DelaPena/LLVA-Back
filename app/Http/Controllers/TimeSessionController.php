<?php

namespace App\Http\Controllers;

use App\Models\TimeSession;
use Illuminate\Http\Request;

class TimeSessionController extends Controller
{
    /**
     * GET /time-sessions/active
     * Returns the authenticated user's currently open session or null.
     */
    public function active(Request $request)
    {
        $session = TimeSession::where('user_id', $request->user()->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        return response()->json($session);
    }

    /**
     * POST /time-sessions
     * Creates a new TimeSession. Returns 409 if one is already open.
     */
    public function store(Request $request)
    {
        $existing = TimeSession::where('user_id', $request->user()->id)
            ->whereNull('ended_at')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'A session is already active.',
                'session' => $existing,
            ], 409);
        }

        $session = TimeSession::create([
            'user_id'    => $request->user()->id,
            'started_at' => now(),
        ]);

        return response()->json($session, 201);
    }

    /**
     * PATCH /time-sessions/{timeSession}
     * Closes the session by setting ended_at and total_idle_seconds.
     */
    public function update(Request $request, TimeSession $timeSession)
    {
        if ($timeSession->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $data = $request->validate([
            'ended_at'           => 'required|date',
            'total_idle_seconds' => 'nullable|integer|min:0',
        ]);

        $timeSession->update($data);

        return response()->json($timeSession);
    }
}
