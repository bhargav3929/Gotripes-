<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;

use Illuminate\Support\Facades\Log;
class CustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $fromEmail;

    public function __construct($data,$fromEmail)
    {
        $this->data = $data;
        $this->fromEmail = $fromEmail;
    }

    /**
     * Override build method to dynamically set "from" address and subject.
     */
    public function build()
    {
        Log::error("logg1",[$this->fromEmail]);
Log::error("logg2",[$this->data['email']]);


        return $this->from($this->fromEmail, $this->data['name'])
         ->replyTo($this->data['email'], $this->data['email']) 

                    ->subject('Contact Form Submission: ' . $this->data['booking_city'])
                    ->view('emails.contact-form');
                   
    }

    // Optional attachments method if you want to add attachments dynamically
    public function attachments(): array
    {
        $attachments = [];

        if (isset($this->data['path'])) {
            $attachments[] = Attachment::fromStorage($this->data['path']);
        }

        return $attachments;
    }
}
