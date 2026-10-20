<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent after a password is reset or changed, so the account holder learns
 * about it even if someone else did it.
 */
class PasswordChanged extends Notification
{
    use Queueable;

    public function __construct(protected ?string $ip = null)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $tenant = current_company();
        $name = $tenant?->name ?? config('app.name');
        $when = now()->format('j M Y, H:i') . ' UTC';

        $mail = (new MailMessage)
            ->from(config('mail.from.address'), $name)
            ->subject("Your {$name} password was changed")
            ->greeting('Hello' . ($notifiable->name ? ' ' . $notifiable->name : '') . ',')
            ->line("The password for your {$name} account ({$notifiable->email}) was changed on {$when}"
                . ($this->ip ? " from IP {$this->ip}." : '.'))
            ->line('All other signed-in sessions for this account have been signed out.')
            ->line('If this was you, there is nothing else to do.')
            ->line('If this was not you, reset your password straight away using the link below, then contact us.')
            ->action('Reset my password', route('password.request'));

        if ($tenant?->email) {
            $mail->replyTo($tenant->email, $name);
        }

        return $mail->salutation($name);
    }
}
