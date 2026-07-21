<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        // Attach only files that are actually on disk. A missing upload used to
        // make attach() throw, which killed the whole message — the customer and
        // the supplier then silently received nothing at all. The application
        // details matter more than the attachment, so skip what we can't read
        // and log it instead of losing the email.
        $attachments = [
            'passport_copy'  => $this->passportCopyPath,
            'passport_photo' => $this->passportPhotoPath,
        ];

        foreach ($attachments as $label => $path) {
            if (!$path) {
                continue;
            }

            // Resolve through the disk rather than assuming storage/app/public.
            // This host points the "public" disk at public/storage (symlink() is
            // disabled on Hostinger), so a hardcoded path looked in the wrong
            // directory and every attachment failed.
            $fullPath = Storage::disk('public')->path($path);

            if (!is_file($fullPath) || !is_readable($fullPath)) {
                Log::warning('Visa email attachment missing — sending without it', [
                    'attachment' => $label,
                    'path'       => $fullPath,
                ]);
                continue;
            }

            $mail->attach($fullPath, [
                'as' => $label . '.' . pathinfo($path, PATHINFO_EXTENSION),
            ]);
        }

        return $mail;
    }
}
