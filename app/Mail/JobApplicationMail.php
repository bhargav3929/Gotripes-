<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $fromEmail;
    public $isForApplicant; // NEW: Flag to differentiate recipient type

    public function __construct($data, $fromEmail, $isForApplicant = false)
    {
        $this->data = $data;
        $this->fromEmail = $fromEmail;
        $this->isForApplicant = $isForApplicant; // NEW: Track if this is for the applicant
    }

    public function build()
    {
        // Different subjects and content based on recipient
        if ($this->isForApplicant) {
            // For the applicant - confirmation email
            $email = $this->from($this->fromEmail, 'Aynalam IR Tourism')
                ->replyTo($this->fromEmail, 'Aynalam IR Tourism')
                ->subject('Thank you for your job application - ' . $this->data['name'])
                ->view('emails.job-application');
        } else {
            // For admins - full application details with buttons
            $email = $this->from($this->fromEmail, $this->data['name'])
                ->replyTo($this->data['email'], $this->data['email'])
                ->subject('Job Application from ' . $this->data['name'])
                ->view('emails.job-application');

            // Attach files only to admin emails
            if (!empty($this->data['resume_path'])) {
                $email->attach(storage_path('app/public/' . $this->data['resume_path']));
            }
            if (!empty($this->data['passport_path'])) {
                $email->attach(storage_path('app/public/' . $this->data['passport_path']));
            }
        }

        return $email;
    }
}
