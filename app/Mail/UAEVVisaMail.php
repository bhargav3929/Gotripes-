<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UAEVVisaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $passportCopyPath;
    public $passportPhotoPath;

    /**
     * Create a new message instance.
     */
    public function __construct($data, $passportCopyPath, $passportPhotoPath)
    {
        $this->data = $data;
        $this->passportCopyPath = $passportCopyPath;
        $this->passportPhotoPath = $passportPhotoPath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->view('emails.uaeapplication') // <-- Use this path or 'emails.application'
                     ->subject('New UAEV Visa Application')
                     ->from(config('mail.from.address'), 'UAEV Visa')
                     ->with('data', $this->data);

        if ($this->passportCopyPath) {
            $mail->attach(storage_path('app/public/' . $this->passportCopyPath), [
                'as' => 'passport_copy.' . pathinfo($this->passportCopyPath, PATHINFO_EXTENSION),
            ]);
        }
        if ($this->passportPhotoPath) {
            $mail->attach(storage_path('app/public/' . $this->passportPhotoPath), [
                'as' => 'passport_photo.' . pathinfo($this->passportPhotoPath, PATHINFO_EXTENSION),
            ]);
        }

        return $mail;
    }
}
