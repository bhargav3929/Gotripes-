<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Password-reset email sent in the name of the tenant the person is signing in
 * to (not the platform's default sender), pointing back at that tenant's own
 * subdomain. Replies go to the tenant's own inbox.
 */
class TenantResetPassword extends ResetPassword
{
    public function __construct(string $token, protected ?string $requestIp = null)
    {
        parent::__construct($token);
    }

    public function toMail($notifiable)
    {
        $tenant = current_company();
        $name = $tenant?->name ?? config('app.name');
        $url = $this->resetUrl($notifiable);
        $minutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        $mail = (new MailMessage)
            ->from(config('mail.from.address'), $name)
            ->subject("Reset your {$name} password")
            ->greeting('Hello' . ($notifiable->name ? ' ' . $notifiable->name : '') . ',')
            ->line("We received a request to reset the password for your {$name} account.")
            ->action('Choose a new password', $url)
            ->line("This link expires in {$minutes} minutes and can be used once.")
            ->line('If you did not ask for this, you can ignore this email; your password stays as it is.');

        if ($this->requestIp) {
            $mail->line('Request made from IP ' . $this->requestIp . ' on ' . now()->format('j M Y, H:i') . ' UTC.');
        }

        if ($tenant?->email) {
            $mail->replyTo($tenant->email, $name);
        }

        return $mail->salutation($name);
    }
}
