<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sent once when an owner/admin creates a customer-care login. Carries the
 * temporary password, so it is never queued or re-sent: the plain password
 * exists only in this message and the one-time flash on the manager screen.
 */
class CustomerCareWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $staff,
        public string $temporaryPassword,
        public string $loginUrl,
        public string $companyName,
    ) {
    }

    public function build(): self
    {
        return $this->subject("Your {$this->companyName} customer care login")
            ->view('emails.customer-care-welcome');
    }
}
