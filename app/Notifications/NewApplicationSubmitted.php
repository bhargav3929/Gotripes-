<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells a company's managers a new self-registration is waiting for review —
 * freelancer or agent, both funnel through here since both are "no access
 * until approved" flows. Delivered by mail and to the manager topbar bell
 * (the `database` channel — see layouts/manager.blade.php for the dropdown).
 */
class NewApplicationSubmitted extends Notification
{
    use Queueable;

    /** @param 'freelancer'|'agent' $type */
    public function __construct(
        public string $type,
        public string $applicantName,
        public string $applicantEmail,
        public string $reviewUrl,
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New {$this->type} application — {$this->applicantName}")
            ->line("{$this->applicantName} ({$this->applicantEmail}) applied to become a GoTrips {$this->type}.")
            ->action('Review application', $this->reviewUrl)
            ->line('No access is granted until you approve it.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'  => "{$this->type}_application",
            'title' => ucfirst($this->type) . ' application — ' . $this->applicantName,
            'body'  => "{$this->applicantName} ({$this->applicantEmail}) is waiting for review.",
            'url'   => $this->reviewUrl,
        ];
    }
}
