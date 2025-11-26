<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProgressChangedNotification extends Notification
{
    use Queueable;

    /**
     * Payload minimal:
     * - type: progress_created | progress_updated | progress_confirmed
     * - project_id, project_name
     * - progress_id, progress_name
     * - percent (opsional; untuk updated/confirmed)
     * - actor_id, actor_name
     * - meta (opsional)
     */
    public function __construct(public array $payload) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $p = $this->payload;

        return [
            'type'          => (string)($p['type'] ?? 'progress_updated'),
            'project_id'    => (int)($p['project_id'] ?? 0),
            'project_name'  => (string)($p['project_name'] ?? '-'),
            'progress_id'   => (int)($p['progress_id'] ?? 0),
            'progress_name' => (string)($p['progress_name'] ?? null),
            'percent'       => isset($p['percent']) ? (int)$p['percent'] : null,
            'actor_id'      => (int)($p['actor_id'] ?? 0),
            'actor_name'    => (string)($p['actor_name'] ?? 'System'),
            'meta'          => $p['meta'] ?? null,
        ];
    }

    // Laravel 11+: opsional untuk nama tipe
    public function databaseType(object $notifiable): string
    {
        return 'progress.changed';
    }
}
