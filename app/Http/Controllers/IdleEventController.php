<?php

namespace App\Http\Controllers;

use App\Models\IdleEvent;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class IdleEventController extends Controller
{
    /**
     * POST /idle-events
     * Creates an idle alarm record when the threshold is first crossed.
     * idle_end is optional — set later via PATCH when the user returns.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'session_id' => 'nullable|integer|exists:time_sessions,id',
            'idle_start' => 'required|date',
            'idle_end'   => 'nullable|date|after:idle_start',
        ]);

        $event = IdleEvent::create([
            'user_id'    => $request->user()->id,
            'session_id' => $data['session_id'] ?? null,
            'idle_start' => $data['idle_start'],
            'idle_end'   => $data['idle_end'] ?? null,
        ]);

        // Notify admins / users with time_tracker → notifications access
        NotificationService::notifyIdleEvent($request->user());

        return response()->json($event, 201);
    }

    /**
     * PATCH /idle-events/{idleEvent}
     * Closes an open idle record by setting idle_end when the user returns.
     */
    public function update(Request $request, IdleEvent $idleEvent)
    {
        if ($idleEvent->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'idle_end' => 'required|date',
        ]);

        $idleEvent->update(['idle_end' => $data['idle_end']]);

        return response()->json($idleEvent);
    }
}
