<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class TimeTrackerEvent extends Notification
{
    private static array $meta = [
        'TIME_IN'         => ['icon' => 'mdi-clock-check-outline', 'color' => 'success'],
        'TIME_OUT'        => ['icon' => 'mdi-clock-out',            'color' => 'info'],
        'BREAK'           => ['icon' => 'mdi-coffee-outline',       'color' => 'warning'],
        'BACK_FROM_BREAK' => ['icon' => 'mdi-coffee-off-outline',   'color' => 'success'],
        'BREAK_OVERTIME'  => ['icon' => 'mdi-alert-circle-outline', 'color' => 'error'],
        'IDLE'            => ['icon' => 'mdi-sleep',                'color' => 'warning'],
    ];

    public function __construct(
        private string $eventType,
        private User   $actor,
        private string $message,
    ) {}

    /** @return array<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        $m = self::$meta[$this->eventType] ?? ['icon' => 'mdi-bell-outline', 'color' => 'primary'];

        return [
            'event_type' => $this->eventType,
            'actor_id'   => $this->actor->id,
            'actor_name' => $this->actor->name,
            'message'    => $this->message,
            'icon'       => $m['icon'],
            'color'      => $m['color'],
        ];
    }
}
