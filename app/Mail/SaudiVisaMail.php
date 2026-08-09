<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Full Saudi Visa application, with the uploaded documents attached. Mirrors
 * UAEVVisaMail: sent to the per-visa-type supplier and company inboxes once
 * payment clears. A missing/unreadable upload is skipped (and logged) rather
 * than throwing and killing the whole message.
 */
class SaudiVisaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $passportPath;
    public $photoPath;
    public $emiratesIdPath;
    public $additionalDocPath;

    public function __construct(
        $data,
        $passportPath = null,
        $photoPath = null,
        $additionalDocPath = null,
        $emiratesIdPath = null
    ) {
        $this->data = $data;
        $this->passportPath = $passportPath;
        $this->photoPath = $photoPath;
        $this->additionalDocPath = $additionalDocPath;
        $this->emiratesIdPath = $emiratesIdPath;
    }

    public function build()
    {
        $mail = $this->view('emails.saudi-visa-application')
                     ->subject('New Saudi Visa Application')
                     ->from(config('mail.from.address'), 'Saudi Visa')
                     ->with('data', $this->data);

        // Attach only files that are actually on disk. Resolve through the disk
        // rather than assuming storage/app/public — this host points the "public"
        // disk at public/storage (symlink() is disabled on Hostinger).
        $attachments = [
            'passport'    => $this->passportPath,
            'photo'       => $this->photoPath,
            'emirates-id' => $this->emiratesIdPath,
            'additional'  => $this->additionalDocPath,
        ];

        foreach ($attachments as $label => $path) {
            if (!$path) {
                continue;
            }

            $fullPath = Storage::disk('public')->path($path);

            if (!is_file($fullPath) || !is_readable($fullPath)) {
                Log::warning('Saudi visa email attachment missing — sending without it', [
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
