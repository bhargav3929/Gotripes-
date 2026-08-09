<?php

namespace App\Mail;

use App\Models\EsimOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The customer's eSIM, sent by GoTrips.
 *
 * MontyeSIM sends its own QR email on assign, but we cannot see whether it
 * arrived, and it carries their branding rather than ours. Since the
 * installation credentials are readable from the order detail endpoint, we
 * send our own copy — so a customer who paid always has an eSIM in their inbox
 * from the company they bought it from.
 *
 * The QR is generated here and embedded in the message itself rather than
 * hotlinked. A remote image URL fails in two common ways: the service can
 * disappear (the first version used Google Image Charts, which Google has since
 * shut down, so every customer would have seen a broken image), and many mail
 * clients block remote images by default. An embedded image has neither
 * problem. The activation details are also printed as text underneath, so the
 * eSIM can always be installed manually.
 */
class EsimQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EsimOrder $order,
        public ?string $activationCode = null,
        public ?string $smdpAddress = null,
        public ?string $matchingId = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your eSIM for ' . ($this->order->country_name ?: 'your trip') . ' — ' . $this->order->order_reference,
        );
    }

    public function content(): Content
    {
        // The LPA string is what an eSIM QR encodes. Prefer the provider's own
        // activation_code; fall back to composing it from its parts.
        $lpa = $this->activationCode
            ?: ($this->smdpAddress && $this->matchingId
                ? 'LPA:1$' . $this->smdpAddress . '$' . $this->matchingId
                : null);

        return new Content(
            view: 'emails.esim-qr',
            with: [
                'order'       => $this->order,
                'lpa'         => $lpa,
                'smdpAddress' => $this->smdpAddress,
                'matchingId'  => $this->matchingId,
                'qrPng'       => $lpa ? $this->qrPng($lpa) : null,
            ],
        );
    }

    /**
     * PNG bytes for the LPA string, or null if the QR could not be built.
     *
     * Never throws: a customer with the manual SM-DP+ details and no picture
     * can still install their eSIM, so a QR failure must not stop the email.
     */
    private function qrPng(string $lpa): ?string
    {
        try {
            return (new \Endroid\QrCode\Builder\Builder(
                writer: new \Endroid\QrCode\Writer\PngWriter(),
                data: $lpa,
                // Medium correction still scans with a little print/screen
                // damage without inflating the code past what phones read well.
                errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::Medium,
                size: 320,
                margin: 12,
            ))->build()->getString();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('eSIM QR image could not be generated', [
                'order_reference' => $this->order->order_reference,
                'error'           => $e->getMessage(),
            ]);
            return null;
        }
    }
}
