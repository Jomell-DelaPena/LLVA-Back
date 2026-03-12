<?php

namespace App\Services;

use App\Events\NotificationBroadcast;
use App\Models\Access;
use App\Models\User;
use App\Notifications\TimeTrackerEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Only these presence status codes trigger notifications.
     * Clock-in / clock-out / break-start / back-from-break are intentionally excluded.
     */
    private static array $notifyStatuses = ['BREAK_OVERTIME'];

    /**
     * Messages for each presence-log status code.
     */
    private static array $presenceMessages = [
        'TIME_IN'         => '%s clocked in',
        'TIME_OUT'        => '%s clocked out',
        'BREAK'           => '%s started a break',
        'BACK_FROM_BREAK' => '%s returned from break',
        'BREAK_OVERTIME'  => '%s exceeded break time',
    ];

    /**
     * Returns all users who should receive Time Tracker notifications,
     * excluding the actor (the employee triggering the event).
     */
    public static function timeTrackerRecipients(User $actor): Collection
    {
        // 1. Users with the global all_access flag
        $allAccessUsers = User::where('all_access', true)
            ->where('id', '!=', $actor->id)
            ->get();

        // 2. Users with the specific time_tracker → notifications access
        $notifAccess = Access::where('code', 'notifications')
            ->whereHas('module', fn($q) => $q->where('code', 'time_tracker'))
            ->first();

        $accessUsers = $notifAccess
            ? User::whereHas('accesses', fn($q) => $q->where('accesses.id', $notifAccess->id))
                  ->where('id', '!=', $actor->id)
                  ->get()
            : collect();

        return $allAccessUsers->merge($accessUsers)->unique('id');
    }

    /**
     * Fires a notification for a presence-log event (clock in/out, break, etc.).
     * Only status codes listed in $notifyStatuses will produce notifications.
     */
    public static function notifyPresenceEvent(User $actor, string $statusCode): void
    {
        if (!in_array($statusCode, self::$notifyStatuses, true)) {
            return;
        }

        $template = self::$presenceMessages[$statusCode] ?? '%s triggered a time event';
        $message   = sprintf($template, $actor->name);

        $recipients = self::timeTrackerRecipients($actor);
        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new TimeTrackerEvent($statusCode, $actor, $message)
        );

        self::broadcastToRecipients($recipients);
    }

    /**
     * Fires a notification when an employee becomes idle.
     */
    public static function notifyIdleEvent(User $actor): void
    {
        $message    = "{$actor->name} has been idle";
        $recipients = self::timeTrackerRecipients($actor);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new TimeTrackerEvent('IDLE', $actor, $message)
        );

        self::broadcastToRecipients($recipients);
    }

    /**
     * Broadcasts the most recently stored notification to each recipient's
     * private WebSocket channel so the frontend receives it instantly.
     */
    private static function broadcastToRecipients(Collection $recipients): void
    {
        foreach ($recipients as $user) {
            $stored = $user->notifications()->latest()->first();
            if ($stored) {
                try {
                    broadcast(new NotificationBroadcast($stored->toArray(), $user->id));
                } catch (\Throwable $e) {
                    \Log::error("[NotificationService] broadcast failed for user {$user->id}: {$e->getMessage()}");
                }
            }
        }
    }
}
