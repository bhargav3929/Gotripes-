<?php

namespace App\Mail;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public string $kind,
        public ?string $messageText = null,
    ) {
    }

    public function build(): self
    {
        $company = $this->ticket->company;
        $companyName = $company?->name ?? config('app.name', 'GoTrips');

        $subject = match ($this->kind) {
            'staff_created' => "New support ticket {$this->ticket->ticket_number}",
            'staff_followup' => "Customer follow-up on {$this->ticket->ticket_number}",
            'customer_reply' => "Update on ticket {$this->ticket->ticket_number}",
            'staff_assigned' => "Ticket {$this->ticket->ticket_number} assigned to you",
            default => "We received your ticket {$this->ticket->ticket_number}",
        };

        $mail = $this->subject($subject)
            ->view('emails.support-ticket')
            ->with(['companyName' => $companyName]);

        if (in_array($this->kind, ['staff_created', 'staff_followup'], true)) {
            $mail->replyTo($this->ticket->customer_email, $this->ticket->customer_name);
        }

        return $mail;
    }
}
